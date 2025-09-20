<?php

namespace App\Interfaces;

use Illuminate\Support\Collection;

interface HasSchedule
{
	/**
	 *  This function returns a collection of Periods that is the schedule for
	 * this model
	 */
	public function getSchedule(): Collection;
	
	/**
	 * This function gets the label that will be applied to each block in the schedule
	 * so the user can see what it refers to.
	 */
	public function getScheduleLabel(): string;
	
	/**
	 * This funcion returns the hex color (in #xxxxxx format) that the block should be colored.
	 */
	public function getScheduleColor(): string;
	
	/**
	 *  This function returns the hex color (in #xxxxxx format) that the text should be colored
	 */
	public function getScheduleTextColor(): string;
	
	/**
	 * this function returns the URL where this schedule should link to.
	 * return NULL if there should be no link.
	 */
	public function getScheduleLink(): ?string;
}
