<?php

namespace App\Classes\Settings;

use App\Casts\IdCard;
use App\Classes\Days;
use App\Models\Locations\Campus;
use App\Models\Utilities\SchoolRoles;
use App\Models\Utilities\SystemSetting;
use Illuminate\Database\Eloquent\Casts\Attribute;

class IdSettings extends SystemSetting
{
    public const ID_STRATEGY_GLOBAL = "global";
    public const ID_STRATEGY_ROLES = "roles";
    public const ID_STRATEGY_CAMPUSES = "campuses";
    public const ID_STRATEGY_BOTH = "both";

    protected static string $settingKey = "school-id-settings";

	protected static function defaultValue(): array
	{
		return
			[
				"id_strategy" => IdSettings::ID_STRATEGY_GLOBAL,
				"global_id" => new IdCard(),
			];
	}

    public function idStrategy(): Attribute
    {
	    return $this->basicProperty('id_strategy');
    }

    public function getGlobalId(): idCard
    {
        if(!isset($this->value['global_id']))
            return new IdCard();
        return IdCard::hydrate($this->value['global_id']);
    }

    public function setGlobalId(idCard $idCard): void
    {
        $idCard->generatePreview();
        $values = $this->value;
        $values['global_id'] = $idCard;
        $this->value = $values;
        $this->save();
    }

    public function getRoleId(SchoolRoles $role): idCard
    {
        if(!isset($this->value['roles'][$role->id]))
            return new IdCard();
        return IdCard::hydrate($this->value['roles'][$role->id]);
    }

    public function setRoleId(SchoolRoles $role, idCard $idCard): void
    {
        $idCard->generatePreview();
        $values = $this->value;
        $values['roles'][$role->id] = $idCard;
        $this->value = $values;
        $this->save();
    }

    public function getCampusId(Campus $campus): idCard
    {
        if(!isset($this->value['campuses'][$campus->id]))
            return new IdCard();
        return IdCard::hydrate($this->value['campuses'][$campus->id]);
    }

    public function setCampusId(Campus $campus, idCard $idCard): void
    {
        $idCard->generatePreview();
        $values = $this->value;
        $values['campuses'][$campus->id] = $idCard;
        $this->value = $values;
        $this->save();
    }

    public function getRoleCampusId(SchoolRoles $role, Campus $campus): idCard
    {
        if(!isset($this->value['both'][$role->id][$campus->id]))
            return new IdCard();
        return IdCard::hydrate($this->value['both'][$role->id][$campus->id]);
    }

    public function setRoleCampusId(SchoolRoles $role, Campus $campus, idCard $idCard): void
    {
        $idCard->generatePreview();
        $values = $this->value;
        $values['both'][$role->id][$campus->id] = $idCard;
        $this->value = $values;
        $this->save();
    }
}
