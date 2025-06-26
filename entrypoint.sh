#!/bin/bash
set -e

# Import the GPG public key from the environment variable
if [ -n "$GPG_PUBLIC_KEY" ]; then
    echo "Importing GPG public key..."
    # Use --batch and --yes to make it non-interactive
    echo -e "$GPG_PUBLIC_KEY" | gpg --batch --yes --import
else
    echo "WARNING: GPG_PUBLIC_KEY environment variable not set."
fi

# Import the GPG private key from the environment variable
if [ -n "$GPG_PRIVATE_KEY" ]; then
    echo "Importing GPG private key..."
    # Use --batch and --yes to make it non-interactive
    echo -e "$GPG_PRIVATE_KEY" | gpg --batch --yes --import
else
    echo "WARNING: GPG_PRIVATE_KEY environment variable not set."
fi

# Trust the key to avoid "unusable public key" errors during encryption
if [ -n "$GPG_RECIPIENT_KEY_ID" ]; then
    echo "Trusting GPG key: $GPG_RECIPIENT_KEY_ID"
    # Use --batch to make this non-interactive
    echo -e "5\ny\n" | gpg --batch --command-fd 0 --edit-key "$GPG_RECIPIENT_KEY_ID" trust
fi

chown -R www-data:www-data /root/.gnupg
# Run database migrations
php artisan migrate --force

# Start Apache in the foreground
apache2-foreground
