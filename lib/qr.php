<?php

use chillerlan\QRCode\{QRCode, QROptions};
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Data\QRMatrix;
use chillerlan\QRCode\Output\QROutputInterface;

require_once(__DIR__ . '/../vendor/autoload.php');

function default_qr_string(): string {
	return "BCD\n001\n1\nSCT\n%s\n%s\n%s\nEUR%s\n%s\n%s\n%s";
}

function format_qr_string(string $bic, string $name, string $iban, string $amount, ?string $short_desc, ?string $refcode, ?string $desc): string {
	return sprintf(default_qr_string(), $bic, $name, $iban, $amount, $short_desc ?? "", $refcode ?? "", $desc ?? "");
}

function qr_blob($bic, $name, $iban, $amount, $short_desc = "", $refcode = "", $desc = "") {
	$options = new QROptions([
		'version'      => 5,
		'outputType'   => QROutputInterface::MARKUP_HTML,
		'eccLevel'     => EccLevel::H,
		'cssClass'     => 'qrcode',
		'moduleValues' => [
			// finder
			QRMatrix::M_FINDER | QRMatrix::IS_DARK     => '#A71111', // dark (true)
			QRMatrix::M_FINDER                         => '#FFBFBF', // light (false)
			QRMatrix::M_FINDER_DOT | QRMatrix::IS_DARK => '#A71111', // finder dot, dark (true)
			// alignment
			QRMatrix::M_ALIGNMENT | QRMatrix::IS_DARK  => '#A70364',
			QRMatrix::M_ALIGNMENT                      => '#FFC9C9',
			// timing
			QRMatrix::M_TIMING | QRMatrix::IS_DARK     => '#98005D',
			QRMatrix::M_TIMING                         => '#FFB8E9',
			// format
			QRMatrix::M_FORMAT | QRMatrix::IS_DARK     => '#003804',
			QRMatrix::M_FORMAT                         => '#00FB12',
			// version
			QRMatrix::M_VERSION | QRMatrix::IS_DARK    => '#650098',
			QRMatrix::M_VERSION                        => '#E0B8FF',
			// data
			QRMatrix::M_DATA | QRMatrix::IS_DARK       => '#4A6000',
			QRMatrix::M_DATA                           => '#ECF9BE',
			// darkmodule
			QRMatrix::M_DARKMODULE | QRMatrix::IS_DARK => '#080063',
			// separator
			QRMatrix::M_SEPARATOR                      => '#AFBFBF',
			// quietzone
			QRMatrix::M_QUIETZONE                      => '#DDDDDD',
		]
	]);
	return (new QRCode($options))->render(format_qr_string($bic, $name, $iban, $amount, $short_desc, $refcode, $desc));
}

?>