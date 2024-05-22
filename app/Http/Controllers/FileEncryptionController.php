<?php

namespace App\Http\Controllers;

use gnupg;
use Illuminate\Http\Request;

class FileEncryptionController extends Controller
{
    public function encrypt(Request $request)
    {
        $request->validate([
            'file_to_encrypt' => 'required|file'
        ]);

        $gpg = new gnupg();
        $gpg->addencryptkey('');

        $fileContent = file_get_contents($request->file('file_to_encrypt')->getPathName());
        $encrypted = $gpg->encrypt($fileContent);

        $encryptedFilePath = storage_path('app/encrypted_file.gpg');
        file_put_contents($encryptedFilePath, $encrypted);

        return response()->download($encryptedFilePath);
    }

    public function decrypt(Request $request)
    {
        $request->validate([
            'file_to_decrypt' => 'required|file'
        ]);

        $gpg = new gnupg();
        $gpg->adddecryptkey('', '');

        $fileContent = file_get_contents($request->file('file_to_decrypt')->getPathName());
        $decrypted = $gpg->decrypt($fileContent);

        $decryptedFilePath = storage_path('app/decrypted_file.txt');
        file_put_contents($decryptedFilePath, $decrypted);

        return response()->download($decryptedFilePath);
    }
}
