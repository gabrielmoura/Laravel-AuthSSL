<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Method Name
    | simple|db
    |--------------------------------------------------------------------------
    */

    'method' => env('AUTHSSL_METHOD', 'simple'),
    'force' => env('AUTHSSL_FORCE', false),
    /*
     * -----------------------------------------------------------
     * Define Certificado CA e chave
     * -----------------------------------------------------------
     */

    'ca' => [
        'cert' => file_get_contents(base_path('ca.crt')),
        'key' => file_get_contents(base_path('ca.key')),
        'password' => env('AUTHSSL_CA_PASS', null)
    ],
    /*
     * Configurações SSL
     */
    'config' => [
        'default' => [
            /*
             * countryName -            Country Name (2 letter code) - eg. CZ
             */
            "countryName" => "BR",
            /*
             * stateOrProvinceName -    State or Province Name (full name) - eg. Czech Republic
             */
            "stateOrProvinceName" => "Rio de Janeiro",
            /*
             * localityName -           Locality Name - eg. Prague
             */
            "localityName" => "Rio de Janeiro",
            /*
             * organizationName -       Organization Name - eg. Whoknows Ltd.
             */
            "organizationName" => "SrMoura",
            /*
             * organizationalUnitName - Organizational Unit Name - eg. PHP Developer.
             */
            "organizationalUnitName" => "TI",
            /*
             * !! IMPORTANT !!
             * commonName -             The Common Name field usually must exactly match the hostname of
             *                          the system the certificate will be used on; otherwise, clients should
             *                          complain about a certificate to hostname mismatch.
             */
            "commonName" => "srmoura.com.br",
            /*
             * emailAddress -           The owener e-mail address - eg. saparov.p@example.com
             */
            "emailAddress" => "webmaster.com.br"
        ],
        'ssl' => [
            /*
             * digest_alg -       Selects which digest method to use.
             */
            "digest_alg" => "md5",
            /*
             * x509_extensions -  Selects which extensions should be used when creating a x509 certificate.
             *                    The extentions to add to the self signed cert.
             */
            "x509_extensions" => "v3_ca",
            /*
             * req_extensions -   Selects which extensions should be used when creating a CSR.
             *                    The extensions to add to a certificate request.
             */
            "req_extensions" => "v3_req",
            /*
             * private_key_bits - Specifies how many bits should be used to generate a private key.
             */
            "private_key_bits" => 1024,
            /*
             * private_key_type - Specifies the type of private key to create. This can be one of OPENSSL_KEYTYPE_DSA,
             *                    OPENSSL_KEYTYPE_DH or OPENSSL_KEYTYPE_RSA. The default value is OPENSSL_KEYTYPE_RSA
             *                    which is currently the only supported key type.
             */
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
            /*
             * encrypt_key -      Should an exported key (with passphrase) be encrypted?
             */
            "encrypt_key" => true,
            /*
             * config -           Path to to the openssl.conf file.
             */
            "config" => "/etc/pki/tls/openssl.cnf"
        ],
    ],


];
