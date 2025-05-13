# GPG Encryption Project Guidelines

## Project Overview
This Laravel application provides a simple and secure way to encrypt and decrypt files using GPG (GNU Privacy Guard) encryption. It leverages the PHP GnuPG extension to handle encryption operations and provides a web interface for users to upload files for encryption and download the encrypted results.

## Installation

### Requirements
- PHP 8.2 or higher
- Laravel 12.1.1.0
- PHP GnuPG extension (`ext-gnupg`)
- GnuPG installed on the server

### Installation Steps
1. Clone the repository:
   ```bash
   git clone [repository-url]
   cd gpg-encryption-project
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Set up environment variables:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Configure GPG keys in your `.env` file:
   ```
   GPG_RECIPIENT_KEY_ID=your_recipient_key_id
   GPG_PRIVATE_KEY_ID=your_private_key_id
   GPG_PASSPHRASE=your_passphrase
   ```

5. Start the development server:
   ```bash
   php artisan serve
   ```

### GPG Key Setup
1. Generate a GPG key pair if you don't have one:
   ```bash
   gpg --full-generate-key
   ```

2. List your keys to find the key IDs:
   ```bash
   gpg --list-keys
   gpg --list-secret-keys
   ```

3. Import the keys into your application's GPG keyring.

## Usage

### Encrypting Files
1. Navigate to the file encryption page in the application.
2. Upload the file you want to encrypt.
3. Submit the form to encrypt the file.
4. The encrypted file will be downloaded automatically.

### Decrypting Files
1. Navigate to the file decryption page in the application.
2. Upload the encrypted file (with .gpg extension).
3. Submit the form to decrypt the file.
4. The decrypted file will be downloaded automatically.

## API Endpoints

### POST /encrypt
Encrypts an uploaded file using GPG.

**Request:**
- Form data with `file_to_encrypt` field containing the file to encrypt.

**Response:**
- The encrypted file as a download.

### POST /decrypt
Decrypts an uploaded GPG-encrypted file.

**Request:**
- Form data with `file_to_decrypt` field containing the encrypted file.

**Response:**
- The decrypted file as a download.

## Security Best Practices

### Key Management
- Store your GPG keys securely and never commit them to version control.
- Use environment variables for key IDs and passphrases.
- Regularly rotate your GPG keys according to your security policy.
- Consider using a key management service for production environments.

### File Handling
- Encrypted files are temporarily stored in the `storage/app` directory. Ensure this directory is properly secured.
- Implement a cleanup mechanism to remove temporary files after they've been downloaded.
- Consider implementing file size limits to prevent denial-of-service attacks.

### Authentication and Authorization
- Implement proper authentication and authorization to restrict access to encryption/decryption functionality.
- Consider implementing rate limiting to prevent abuse.

## Troubleshooting

### Common Issues
1. **GnuPG extension not found**: Ensure the PHP GnuPG extension is installed and enabled.
   ```bash
   sudo apt-get install php-gnupg  # For Debian/Ubuntu
   # or
   sudo yum install php-gnupg      # For CentOS/RHEL
   ```

2. **Key not found errors**: Verify that the key IDs in your `.env` file match the keys in your GPG keyring.

3. **Permission issues**: Ensure the web server has read/write access to the storage directory.

## Contributing
1. Fork the repository.
2. Create a feature branch: `git checkout -b feature-name`.
3. Commit your changes: `git commit -m 'Add some feature'`.
4. Push to the branch: `git push origin feature-name`.
5. Submit a pull request.

### Coding Standards
- Follow PSR-12 coding standards.
- Write unit tests for new features.
- Ensure all tests pass before submitting a pull request.

## License
This project is licensed under the MIT License - see the LICENSE file for details.
