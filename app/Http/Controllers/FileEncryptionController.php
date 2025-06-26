<?php

namespace App\Http\Controllers;

use gnupg;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileEncryptionController extends Controller
{
    /**
     * Encrypts the uploaded file and sets the download name by appending .gpg.
     */
    public function encrypt(Request $request): BinaryFileResponse
    {
        $request->validate([
            'file_to_encrypt' => 'required|file'
        ]);

        $originalFile = $request->file('file_to_encrypt');
        $downloadFilename = $originalFile->getClientOriginalName() . '.gpg';

        $gpg = new gnupg();
        $gpg->addencryptkey(env('GPG_RECIPIENT_KEY_ID'));

        $fileContent = File::get($originalFile->getPathName());
        $encrypted = $gpg->encrypt($fileContent);

        if ($encrypted === false) {
            return response()->json(['error' => 'Encryption failed. Please ensure the recipient GPG key is valid and imported correctly on the server.'], 500);
        };

        $encryptedFilePath = storage_path('app/' . uniqid('encrypted_', true) . '.gpg');
        File::put($encryptedFilePath, $encrypted);

        // Download the file with the correct name and content type, then delete it from the server
        return response()->download(
            $encryptedFilePath,
            $downloadFilename,
            ['Content-Type' => 'application/pgp-encrypted']
        )->deleteFileAfterSend();
    }

    public function decrypt(Request $request): BinaryFileResponse|JsonResponse
    {
        $request->validate([
            'file_to_decrypt' => 'required|file|mimetypes:application/pgp-encrypted,application/octet-stream'
        ]);

        $encryptedFile = $request->file('file_to_decrypt');
        $encryptedFilename = $encryptedFile->getClientOriginalName();

        $downloadFilename = preg_replace('/\.gpg$/i', '', $encryptedFilename);

        if ($downloadFilename === $encryptedFilename) {
            return response()->json(['error' => 'Invalid file. A .gpg file is required.'], 422);
        }

        $gpg = new gnupg();
        $gpg->adddecryptkey(env('GPG_PRIVATE_KEY_ID'), env('GPG_PASSPHRASE'));

        $fileContent = File::get($encryptedFile->getPathName());
        $decrypted = $gpg->decrypt($fileContent);

        if ($decrypted === false) {
            return response()->json(['error' => 'Decryption failed. Please check your key and passphrase.'], 422);
        }

        $decryptedFilePath = storage_path('app/' . uniqid('decrypted_', true));
        File::put($decryptedFilePath, $decrypted);

        return response()->download(
            $decryptedFilePath,
            $downloadFilename
        )->deleteFileAfterSend();
    }
}
