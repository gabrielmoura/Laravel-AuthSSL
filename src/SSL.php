<?php

namespace Gabrielmoura\Laravel-AuthSSL;


/**
 * Class SSL
 * @package Blx32\auth_ssl
 * @author Gabriel Moura <g@srmoura.com.br>
 * @copyright 2015-2017 SrMoura
 * @license http://srmoura.com.br/license Proprietária
 * Respeite a licença do proprietário.
 */
class SSL
{
    protected $cert;
    protected $resource;
    protected $config;
    private $file;
    private $key;
    private $password;
    private $private;

    public function __construct()
    {
        $this->cert = config('authssl.ca.cert');
        $this->key = config('authssl.ca.key');
        $this->password = config('authssl.ca.password');
        $this->config = config('authssl.config');
    }

    /**
     * @param bool $shotnames
     * @return mixed|array
     */
    public function caInfo($shotnames = false)
    {
        $this->resource = openssl_x509_read($this->cert);
        $certInfo = openssl_x509_parse($this->resource, $shotnames);
        return $certInfo['subject'];
    }

    /**
     * @param $cert
     * @param bool $shotnames
     * @return mixed
     */
    public function certInfo($cert, $shotnames = false)
    {
        $this->resource = openssl_x509_read($cert);
        $certInfo = openssl_x509_parse($this->resource, $shotnames);
        return $certInfo['subject'];
    }


    /**
     * @param $dataForEncryption Dados
     * @return array
     */
    public function encrypt($dataForEncryption)
    {
        openssl_seal($dataForEncryption, $cryptedData, $envelope_Key, [$this->cert]);
        return [base64_encode($cryptedData), base64_encode($envelope_Key['0'])];
    }

    /**
     * @param $data Dados
     * @return string
     */
    public function cypher($data)
    {
        openssl_public_encrypt($data, $encrypted, $this->cert);
        return base64_encode($encrypted);
    }

    /**
     * @param $data Cifrado
     * @return mixed
     */
    public function decypher($data)
    {
        $this->private = openssl_get_privatekey($this->key, $this->password);
        openssl_private_decrypt(base64_decode($data), $newsource, $this->private);
        return $newsource;
    }

    /**
     * @param $cryptedData Cifrado
     * @param $envelope_Key Cifra_Key
     * @return mixed
     */
    public function decrypt($cryptedData, $envelope_Key)
    {
        openssl_open($cryptedData, $decryptedData, $envelope_Key, $this->cert);
        return $decryptedData;
    }

    /**
     * @param $dataToSign
     * @param $algorithm
     * @return string
     */
    public function signature($dataToSign, $algorithm = OPENSSL_ALGO_SHA1)
    {
        $this->private = openssl_get_privatekey($this->key, $this->password);
        openssl_sign($dataToSign, $signature, $this->private, $algorithm);
        $this->signature = $signature;
        return base64_encode($signature);
    }

    /**
     * @param $dataToCheck
     * @param $signature
     * @param int $algorithm
     * @return int
     */
    public function verify($dataToCheck, $signature, $algorithm = OPENSSL_ALGO_SHA1)
    {
        return openssl_verify($dataToCheck, base64_decode($signature), $this->cert, $algorithm);
    }

    /**
     * Cria certificado
     * @param array $data
     * @return resource
     * ["countryName" => "CZ",
     * "stateOrProvinceName" => "Czech Republic",
     * "localityName" => "Prague",
     * "organizationName" => "Whoknows Ltd.",
     * "organizationalUnitName" => "PHP Developer",
     * "commonName" => "HelloWorld",
     * "emailAddress" => "saparov.p@example.com"]
     */
    public function certCreate(array $data)
    {
        return openssl_pkey_new($data);
    }

    /**
     * Cria o CSR
     * @param $data CertGerado
     * @return mixed
     */
    public function csrCreate($data)
    {
        return openssl_csr_new($data, $this->cert, $this->config['ssl']);
    }

    /**
     * Assina o Certificado
     * @param $data CSR
     * @param int $selfsigned 0
     * @param string $validDays NumDays
     * @return resource
     */
    public function certAssign($data, $selfsigned = 0, $validDays = '365')
    {
        return openssl_csr_sign($data,
            ($selfsigned == 0 ? $selfsigned : null),
            [
                $this->cert,
                $this->password
            ],
            $validDays,
            $this->config['ssl']);
    }

    /**
     * @param $data
     * @param string $filename
     * @param bool $notreadable
     * @return bool
     */
    public function exportCert($data, $filename = 'default.crt', $notreadable = true)
    {
        return openssl_x509_export_to_file($data, $filename, $notreadable);
    }

    /**
     * @param $data Key
     * @param string $filename
     * @param null $passphrase
     * @return bool
     */
    public function exportKey($data, $filename = 'default.key', $passphrase = null)
    {
        return openssl_pkey_export_to_file($data, $filename, $passphrase, $this->config['ssl']);

    }

    /**
     * @param $data CSR
     * @param string $filename
     * @param bool $notreadable
     * @return bool
     */
    public function exportCsr($data, $filename = 'default.csr', $notreadable = true)
    {

        return openssl_csr_export_to_file($data, $filename, $notreadable);

    }

    /**
     * @param $data
     * @param bool $notreadable
     * @return mixed
     */
    public function exportStringCert($data, $notreadable = true)
    {
        openssl_x509_export($data, $output, $notreadable);
        return $output;
    }

    /**
     * @param $data Key
     * @param null $passphrase
     * @return mixed
     */
    public function exportStringKey($data, $passphrase = null)
    {
        openssl_pkey_export($data, $output, $passphrase, $this->config['ssl']);
        return $output;
    }

    /**
     * @param $data CSR
     * @param bool $notreadable
     * @return mixed
     */
    public function exportStringCsr($data, $notreadable = true)
    {
        $output = '';
        openssl_csr_export_to_file($data, $output, $notreadable);
        return $output;
    }
}

/* Exemplo de uso.
echo "<br><br><br>";
echo $data = "Deus é Fiel";
$ar = $OpenSSL->encrypt($data);
echo "<br>";
echo $ar[0];
echo "<br>";
echo $ar[1];
echo "<br>";

echo "<br><br><br>";
echo $cyphed = $OpenSSL->cypher($data);
//echo $OpenSSL->decrypt($ar['0'], $ar['1']);
echo "<br><br><br>";
echo $OpenSSL->decypher($cyphed);
echo "<br><br><br>";
echo var_dump($OpenSSL->certinfo());
echo "<br>";
echo $OpenSSL->certinfo()['commonName'];

echo "<br><br><br>";
echo "<br>";
echo 'Assinatura: '. $signature = $OpenSSL->signature($data);
echo "<br>";
if ($OpenSSL->verify($data, $signature)) {
    echo "signature is OK!\n";
}
echo "<br><br><br>";
*/
