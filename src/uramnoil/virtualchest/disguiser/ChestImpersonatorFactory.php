<?php


namespace uramnoil\virtualchest\disguiser;


interface ChestImpersonatorFactory {
	public function create() : ChestImpersonator;
}