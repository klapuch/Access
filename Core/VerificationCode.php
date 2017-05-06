<?php
declare(strict_types = 1);
namespace Klapuch\Access;

use Klapuch\Output;

interface VerificationCode {
	/**
	 * Use the verification code
	 * @throws \Exception
	 * @return void
	 */
	public function use(): void;

	/**
	 * Print the code
	 * @param \Klapuch\Output\Format $format
	 * @return \Klapuch\Output\Format
	 */
	public function print(Output\Format $format): Output\Format;
}