<?php

namespace App\Http\Controllers;

use gnupg;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileEncryptionController extends Controller
{
    // Add this helper method
    private function initializeGpg(): gnupg
    {
        // Check for a custom GPG home directory from the environment file
        $gpgHome = env('GPG_HOME_DIR');
        if ($gpgHome) {
            putenv("GNUPGHOME=$gpgHome");
        }

        // The constructor will now use the custom path on Render,
        // or the default path on your local machine.
        return new gnupg();
    }

    public function encrypt(Request $request): BinaryFileResponse|JsonResponse // Added JsonResponse for error
    {
        $request->validate([
            'file_to_encrypt' => 'required|file'
        ]);

        $originalFile = $request->file('file_to_encrypt');
        $downloadFilename = $originalFile->getClientOriginalName() . '.gpg';

        // Use the helper method
        $gpg = $this->initializeGpg();
        $gpg->addencryptkey(env('GPG_RECIPIENT_KEY_ID'));

        $fileContent = File::get($originalFile->getPathName());
        $encrypted = $gpg->encrypt($fileContent);

        // Your existing error handling for this is now crucial
        if ($encrypted === false) {
            return response()->json(['error' => 'Encryption failed. GPG error: ' . $gpg->geterror()], 500);
        }

        $encryptedFilePath = storage_path('app/' . uniqid('encrypted_', true) . '.gpg');
        File::put($encryptedFilePath, $encrypted);

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

        // Use the helper method here as well
        $gpg = $this->initializeGpg();
        $gpg->adddecryptkey(env('GPG_PRIVATE_KEY_ID'), env('GPG_PASSPHRASE'));

        $fileContent = File::get($encryptedFile->getPathName());
        $decrypted = $gpg->decrypt($fileContent);

        if ($decrypted === false) {
            // Also improved error reporting here
            return response()->json(['error' => 'Decryption failed. GPG error: ' . $gpg->geterror()], 422);
        }

        $decryptedFilePath = storage_path('app/' . uniqid('decrypted_', true));
        File::put($decryptedFilePath, $decrypted);

        return response()->download(
            $decryptedFilePath,
            $downloadFilename
        )->deleteFileAfterSend();
    }
}
