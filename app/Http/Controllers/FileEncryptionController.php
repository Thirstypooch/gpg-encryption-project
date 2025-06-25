<?php

namespace App\Http\Controllers;

use gnupg;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileEncryptionController extends Controller
{
    public function encrypt(Request $request): BinaryFileResponse
    {
        $request->validate([
            'file_to_encrypt' => 'required|file'
        ]);

        $originalFile = $request->file('file_to_encrypt');
        $originalFileName = pathinfo($originalFile->getClientOriginalName(), PATHINFO_FILENAME);
        $downloadFilename = $originalFileName . '.gpg';

        $gpg = new gnupg();
        $gpg->addencryptkey(env('GPG_RECIPIENT_KEY_ID'));

        $fileContent = File::get($originalFile->getPathName());
        $encrypted = $gpg->encrypt($fileContent);

        $encryptedFilePath = storage_path('app/' . uniqid('encrypted_', true) . '.gpg');
        File::put($encryptedFilePath, $encrypted);

        return response()->download(
            $encryptedFilePath,
            $downloadFilename,
            ['Content-Type' => 'application/octet-stream']
        )->deleteFileAfterSend();
    }

    public function decrypt(Request $request): BinaryFileResponse|JsonResponse
    {
        $request->validate([
            'file_to_decrypt' => 'required|file|mimetypes:application/pgp-encrypted,application/octet-stream'
        ]);

        $encryptedFile = $request->file('file_to_decrypt');
        $originalFileName = pathinfo($encryptedFile->getClientOriginalName(), PATHINFO_FILENAME);
        // Assume the decrypted file is a text file, adjust if necessary
        $downloadFilename = $originalFileName . '.txt';

        $gpg = new gnupg();
        $gpg->adddecryptkey(env('GPG_PRIVATE_KEY_ID'), env('GPG_PASSPHRASE'));

        $fileContent = File::get($encryptedFile->getPathName());
        $decrypted = $gpg->decrypt($fileContent);

        if ($decrypted === false) {
            return response()->json(['error' => 'Decryption failed. Check your key and passphrase.'], 422);
        }

        $decryptedFilePath = storage_path('app/' . uniqid('decrypted_', true) . '.txt');
        File::put($decryptedFilePath, $decrypted);

        return response()->download(
            $decryptedFilePath,
            $downloadFilename,
            ['Content-Type' => 'text/plain']
        )->deleteFileAfterSend();
    }
}
