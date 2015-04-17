<?php
	/**
	* An RSA Implementation in PHP
	*
	* @author Robert Crossfield <robcrossfield@gmail.com>
	*/

class cRSA {
	 
	private $P;
	private $Q;

	private $N;

	private $E;
	private $D;

	private $Totient;

	/**
	 * Constructor
	 *
	 * @param pPrimeP
	 * @param pPrimeQ
	 */
	public function __construct( $pPrimeP = 0, $pPrimeQ = 0) {
		$this->D  = 1;
		 
		while( $this->GenerateKey( $pPrimeP, $pPrimeQ ) == 1) {
			echo "Private Exponent == 1\nRetrying\n\n";
		}
	}

	/** 
	 * Generate a private/public key
	 *
	 * @param pPrimeP
	 * @param pPrimeQ
	 */
	private function GenerateKey( $pPrimeP, $pPrimeQ ) {
		
		if( $pPrimeP == 0 )		   
			$pPrimeP = getPrime( rand (10000, 20000) );
	
		if( $pPrimeQ == 0 ) 
			$pPrimeQ = getPrime( rand ($pPrimeP, $pPrimeP + 10000) );
 
		echo "Found Primes: $pPrimeP and $pPrimeQ\n\n";
		
		$this->P = $pPrimeP;
		$this->Q = $pPrimeQ;
		$this->N = bcmul($this->P, $this->Q);
	   
		$this->E = 17;
	   
		$this->Totient = bcmul(($this->P - 1), ($this->Q - 1));
	   
		$this->D = $this->inverse_mod( $this->E, $this->Totient ); 
		
		return $this->D;
	}
	
	/**
	 * Get an array of all the parameters being used
	 *
	 * @return array
	 */
	public function OutputBits() {
		$Output['P'] = $this->P;
		$Output['Q'] = $this->Q;
		$Output['N'] = $this->N;
		$Output['E'] = $this->E;
	   
		$Output['D'] = $this->D;
	   
		$Output['Totient'] = $this->Totient;
	   
		return $Output;
	}

	/**
	 * Calculate the Modular Multiplicative Inverse
	 *
	 * @param pA
	 * @param pB
	 *
	 * @return integer
	 */
	function inverse_mod($pA,$pN) {
	   
		$n = $pN;
		$x = 0;
		$newx = 1;
		
		$y= 1;
		$newy = 0;
	   
		while ($pN) {
			
			$t = $pN;
		   
			$q  = bcdiv($pA, $pN, 0);
			
			$pN = bcmod($pA, $pN);
			$pA = $t;
		   
			$t = $x; 
			$x = bcsub( $newx, bcmod( bcmul($q, $x), $n));
			$newx = $t;
			
			$t = $y; 
			$y = bcsub( $newy, bcmod( bcmul($q, $y), $n));
			$newy = $t;
		}
		
		if (bccomp($newx,0) == -1)
			$newx = bcadd($newx, $n);
	   
		return $newx;
	}
	
	/**
	 * Encrypt with the public key
	 *
	 * @param pData
	 *
	 * @return string
	 */
	private function EncPublicKey( $pData ) {
		
		// ($pData ^ E) % N
		return bcpowmod($pData, $this->E, $this->N);
	}

	/**
	 * Decrypt with the private key
	 *
	 * @param pData
	 *
	 * @return string
	 */
	private function DecPrivateKey( $pData ) {

		// ($pData ^ D) % N
		$Output = bcpowmod($pData, $this->D, $this->N);

		return $Output;
	}

	/**
	 * Encrypt a message using the public key
	 *
	 * @param pMessage
	 *
	 * @return Encrypted String
	 */
	public function Encrypt( $pMessage ) {
	   
		$Output = '';
	   
		// Loop until no message remains
		while( strlen( $pMessage ) ) {

			// Grab 1 byte
			$Byte = unpack("C", $pMessage);
			$pMessage = substr( $pMessage, 1 );
		   
			// Encrypt it, and pack the result as a 32bit integer into the output
			$Output .= pack("L", $this->EncPublicKey( $Byte[1] ));
		   
		}
	   
		return $Output;
	}

	/**
	 * Decrypt a message using the private key
	 *
	 * @param pEncryptedMessage
	 */
	public function Decrypt( $pEncryptedMessage ) {
		$Output = '';
	   
		// Loop until no message remains
		while( strlen($pEncryptedMessage) ) {

			// grab a 32bit
			$Piece = unpack("L", $pEncryptedMessage);
			$pEncryptedMessage = substr( $pEncryptedMessage, 4 );

			// Decrypt it, and pack the result as a byte into the output
			$Output .= pack("C", $this->DecPrivateKey( $Piece[1] ));
		}
	   
		return $Output;
	}
	}

	/**
	* Get a prime number
	*
	* @param pValue The Starting value to look for a prime
	*
	* @return integer
	*/
	function getPrime( $pValue ) {

	while( !isPrime( $pValue ) ) {

		$pValue++;
	}

	return $pValue;
	}

	/**
	* Is a number prime
	*
	* @param pValue Integer to test
	*
	* @return boolean
	*/
	function isPrime($pValue) {
	if($pValue == 2)
		return true;

	if($pValue % 2 == 0 || $pValue == 1 ) {
		return false;
	}

	for($i = 3; $i <= ceil(sqrt($pValue)); $i = $i + 2) {
		if($pValue % $i == 0)
			return false;
	}

	return true;
	}
$PrimeP = 0;
$PrimeQ = 0;

// Create RSA using the primes
$Rsa = new cRSA( $PrimeP, $PrimeQ );

// Display all the parameters being used
foreach( $Rsa->OutputBits() as $Key => $Value ) {
echo "$Key: $Value\n";
}

echo "\n";


$Message = "AAAZXZZZZZZZZZZZZZZZZZB";
$Output = $Rsa->Encrypt($Message);

echo "Encrypting: " . $Message . "\n";
echo "Encrypted (base64): " . base64_encode($Output) . "\n";
echo "Decrypted: " . $Rsa->Decrypt($Output) . "\n";
