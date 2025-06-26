#!/bin/bash

# Exit immediately if a command exits with a non-zero status.
set -e

# Import the GPG public key from the environment variable
# Check if the variable is set to avoid errors
if [ -n "$GPG_PUBLIC_KEY" ]; then
    echo "Importing GPG public key..."
    echo -e "$GPG_PUBLIC_KEY" | gpg --import
else
    echo "WARNING: GPG_PUBLIC_KEY environment variable not set."
fi

# Import the GPG private key from the environment variable
if [ -n "$GPG_PRIVATE_KEY" ]; then
    echo "Importing GPG private key..."
    echo -e "$GPG_PRIVATE_KEY" | gpg --batch --import
else
    echo "WARNING: GPG_PRIVATE_KEY environment variable not set."
fi

# Trust the key to avoid "unusable public key" errors during encryption
# This uses the key ID from your .env file
if [ -n "$GPG_RECIPIENT_KEY_ID" ]; then
    echo "Trusting GPG key: $GPG_RECIPIENT_KEY_ID"
    echo -e "5\ny\n" | gpg --command-fd 0 --edit-key "$GPG_RECIPIENT_KEY_ID" trust
fi

# Run database migrations
php artisan migrate --force

# Start Apache in the foreground
apache2-foreground
