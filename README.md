# GPG Encryption Tool

A modern, intuitive, and visually appealing Single Page Application (SPA) for GPG file encryption and decryption, built with Vue.js, TypeScript, and Laravel.

## Features

- **Modern SPA Interface**: Built with Vue.js and TypeScript for a responsive and interactive user experience
- **Dual Operations**: Clearly distinct sections for file encryption and decryption
- **Drag-and-Drop File Handling**: Intuitive file upload with drag-and-drop functionality
- **Real-time Progress Tracking**: Visual feedback for file upload and processing
- **Elegant Notifications**: User-friendly success and error messages
- **Responsive Design**: Works well on all screen sizes

## Screenshots

![GPG Encryption Tool](screenshot.png)

## Technology Stack

- **Frontend**:
  - Vue.js 3 with Composition API
  - TypeScript
  - Tailwind CSS for styling
  - Axios for API requests
  - Vite for build tooling

- **Backend**:
  - Laravel
  - PHP GnuPG extension for encryption/decryption

## Installation

### Prerequisites

- PHP 8.2 or higher
- Laravel 12.1.1.0
- PHP GnuPG extension (`ext-gnupg`)
- GnuPG installed on the server
- Node.js and npm

### Setup Steps

1. Clone the repository:
   ```bash
   git clone [repository-url]
   cd gpg-encryption-project
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Install JavaScript dependencies:
   ```bash
   npm install
   ```

4. Set up environment variables:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. Configure GPG keys in your `.env` file:
   ```
   GPG_RECIPIENT_KEY_ID=your_recipient_key_id
   GPG_PRIVATE_KEY_ID=your_private_key_id
   GPG_PASSPHRASE=your_passphrase
   ```

6. Build the frontend assets:
   ```bash
   npm run build
   ```

7. Start the development server:
   ```bash
   php artisan serve
   ```

8. Visit `http://localhost:8000` in your browser to use the application.

## Development

For development with hot-reloading:

```bash
npm run dev
```

## Usage

1. **Encrypting Files**:
   - Drag and drop a file onto the encryption panel or click to select a file
   - Click the "Encrypt File" button
   - Wait for the encryption process to complete
   - Download the encrypted file from the success notification

2. **Decrypting Files**:
   - Drag and drop a .gpg file onto the decryption panel or click to select a file
   - Click the "Decrypt File" button
   - Wait for the decryption process to complete
   - Download the decrypted file from the success notification

## Security Considerations

- Store your GPG keys securely and never commit them to version control
- Use environment variables for key IDs and passphrases
- Regularly rotate your GPG keys according to your security policy
- Consider implementing authentication to restrict access to the application

## License

This project is licensed under the MIT License - see the LICENSE file for details.
