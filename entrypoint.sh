#!/bin/bash
set -e

# Define and create a GPG home directory
export GNUPGHOME="/var/www/html/.gnupg"
mkdir -p "$GNUPGHOME"
chmod 700 "$GNUPGHOME"
chown -R www-data:www-data "$GNUPGHOME"

# Import keys as the 'www-data' user
if [ -n "$GPG_PUBLIC_KEY" ]; then
    echo "Importing GPG public key..."
    echo -e "$GPG_PUBLIC_KEY" | gosu www-data gpg --batch --yes --import
fi

if [ -n "$GPG_PRIVATE_KEY" ]; then
    echo "Importing GPG private key..."
    echo -e "$GPG_PRIVATE_KEY" | gosu www-data gpg --batch --yes --import
fi

# Trust the key as the 'www-data' user
if [ -n "$GPG_RECIPIENT_KEY_ID" ]; then
    echo "Trusting GPG key: $GPG_RECIPIENT_KEY_ID"
    echo -e "5\ny\n" | gosu www-data gpg --batch --command-fd 0 --edit-key "$GPG_RECIPIENT_KEY_ID" trust
fi

# Clear Laravel's configuration cache
php artisan config:clear

# Run database migrations
php artisan migrate --force

# Start Apache in the foreground
apache2-foreground
