<?php

namespace App\Classes\Settings;

use App\Casts\IdCard;
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
    protected $table = "system_settings";
    private static string $key = "school-id-settings";
    private static ?IdSettings $instance = null;
    public $timestamps = false;

    public static function instance(): IdSettings
    {
        if(self::$instance === null)
            self::$instance = IdSettings::find(self::$key);
        if(self::$instance === null)
        {
            //in this case, there's no data, so make an empty space
            $setting = new IdSettings();
            $setting->name = self::$key;
            $setting->value = [];
            $setting->save();
            self::$instance = $setting;
        }
        return self::$instance;
    }

    private function updateValue(string $values, string $key, mixed $value)
    {
        $data = json_decode($values, true);
        $data[$key] = $value;
        return ['value' => json_encode($data)];
    }

    private function getValue(string $values, string $key, mixed $default = null)
    {
        $data = json_decode($values, true);
        return $data[$key] ?? $default;
    }

    public function idStrategy(): Attribute
    {
        return Attribute::make
        (
            get: fn(mixed $value, array $attributes) =>
                $this->getValue($attributes['value'], 'id_strategy', self::ID_STRATEGY_GLOBAL),
            set: fn(mixed $value, array $attributes) =>
                $this->updateValue($attributes['value'], 'id_strategy', $value),
        );
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
