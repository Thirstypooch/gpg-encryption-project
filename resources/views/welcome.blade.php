<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Encryption</title>
</head>
<body>
<h1>Encrypt a File</h1>
<form action="/encrypt" method="post" enctype="multipart/form-data">
    @csrf
    <label for="file_to_encrypt">Select file to encrypt:</label>
    <input type="file" name="file_to_encrypt" id="file_to_encrypt" required>
    <button type="submit">Encrypt</button>
</form>

<h1>Decrypt a File</h1>
<form action="/decrypt" method="post" enctype="multipart/form-data">
    @csrf
    <label for="file_to_decrypt">Select file to decrypt:</label>
    <input type="file" name="file_to_decrypt" id="file_to_decrypt" required>
    <button type="submit">Decrypt</button>
</form>
</body>
</html>
