<?php
class FieldEncryptor 
{
    // AES-256-GCM is highly recommended. It handles both encryption and authentication.
    private const CIPHER = 'aes-256-gcm';

    public static function encrypt(string $plaintext, string $key): string 
    {
        // 1. Generate a secure, random Initialization Vector (IV)
        // For GCM, a 12-byte (96-bit) IV is the cryptographic standard
        $ivLength = openssl_cipher_iv_length(self::CIPHER); // Returns 12
        $iv = openssl_random_pseudo_bytes($ivLength);

        // 2. Encrypt the data. 
        // We pass $tag by reference; OpenSSL will populate it with the authentication tag
        $ciphertext = openssl_encrypt(
            $plaintext, 
            self::CIPHER, 
            $key, 
            OPENSSL_RAW_DATA, 
            $iv, 
            $tag
        );

        // 3. Package the IV, the Tag, and the Ciphertext together so you can decrypt it later
        // We base64 encode the final string so it is safe to store in a database or text file
        return base64_encode($iv . $tag . $ciphertext);
    }

    public static function decrypt(string $encryptedData, string $key): ?string 
    {
        $raw = base64_decode($encryptedData);
        
        $ivLength = openssl_cipher_iv_length(self::CIPHER); // 12 bytes
        $tagLength = 16; // GCM auth tags are 16 bytes by default

        // Extract the pieces from the combined string
        $iv = substr($raw, 0, $ivLength);
        $tag = substr($raw, $ivLength, $tagLength);
        $ciphertext = substr($raw, $ivLength + $tagLength);

        // Decrypt and verify the authentication tag automatically
        $plaintext = openssl_decrypt(
            $ciphertext, 
            self::CIPHER, 
            $key, 
            OPENSSL_RAW_DATA, 
            $iv, 
            $tag
        );

        // If the data was tampered with, openssl_decrypt returns false
        return $plaintext !== false ? $plaintext : null;
    }
}
?>