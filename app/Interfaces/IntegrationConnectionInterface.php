<?php

namespace App\Interfaces;

interface IntegrationConnectionInterface
{
	/*****************************************
	 * STATIC ABSTRACT FUNCTIONS
	 */
	
	/**
	 * @return array The default data to save to the instance when a connection is first established to the system.
	 */
	public static function getSystemInstanceDefault(): array;
	/**
	 * @return array The default data to save to the instance when a connection is first established.
	 */
	public static function getInstanceDefault(): array;
}