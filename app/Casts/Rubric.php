<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use JsonSerializable;

class Rubric implements CastsAttributes, Arrayable, JsonSerializable
{
    public array $criteria, $points, $descriptions;


    public function __construct()
    {
        $this->criteria = [];
        $this->points = [];
        $this->descriptions = [];
    }

    public function addCriteria()
    {
        $this->criteria[] = __('subjects.skills.rubric.builder.criteria.change');
        $descriptionRow = [];
        foreach($this->points as $score)
            $descriptionRow[] = __('subjects.skills.rubric.builder.description.change');
        $this->descriptions[] = $descriptionRow;
    }

    public function reorderCriteria(array $newOrder)
    {
        $criteria = $this->criteria;
        $descriptions = $this->descriptions;
        foreach($newOrder as $pos => $newPos)
        {
            $criteria[$newPos] = $this->criteria[$pos];
            $descriptions[$newPos] = $this->descriptions[$pos];
        }
        $this->criteria = $criteria;
        $this->descriptions = $descriptions;
    }

    public function removeCriteria(int $pos)
    {
        array_splice($this->criteria, $pos, 1);
        array_splice($this->descriptions, $pos, 1);
    }

    public function addPoint(float $score)
    {
        if($score < 0)
            return;
        //first, we find the pos
        //pos, can never be 0, as that will be max_poinst, or last element, as that will be 0
        $points = [];
        $pos = -1;
        for($i = 0; $i < count($this->points); $i++)
        {
            if($score == $this->points[$i])
                return;
            if($this->points[$i] < $score && $pos == -1)
            {
                $points[] = $score;
                $pos = $i;
            }
            $points[] = $this->points[$i];
        }
        if($pos == -1)
        {
            $points[] = $score;
        }

        $descriptions = [];
        for($i = 0; $i < count($this->descriptions); $i++)
        {
            $descriptionRow = [];
            for($j = 0; $j < count($this->descriptions[$i]); $j++)
            {
                if($j == $pos)
                    $descriptionRow[] = __('subjects.skills.rubric.builder.description.change');
                $descriptionRow[] = $this->descriptions[$i][$j];
            }
            if($pos == -1)
                $descriptionRow[] = __('subjects.skills.rubric.builder.description.change');
            $descriptions[] = $descriptionRow;
        }
        $this->points = $points;
        $this->descriptions = $descriptions;
    }

    public function removePoint($score)
    {
        if($score < 0)
            return;
        //first, we find the pos
        //pos, can never be 0, as that will be max_poinst, or last element, as that will be 0
        $points = [];
        $pos = -1;
        for($i = 0; $i < count($this->points); $i++)
        {
            if($this->points[$i] == $score && $pos == -1)
            {
                $pos = $i;
                continue;
            }
            $points[] = $this->points[$i];
        }

        $descriptions = [];
        for($i = 0; $i < count($this->descriptions); $i++)
        {
            $descriptionRow = [];
            for($j = 0; $j < count($this->descriptions[$i]); $j++)
            {
                if($j == $pos)
                    continue;
                $descriptionRow[] = $this->descriptions[$i][$j];
            }
            $descriptions[] = $descriptionRow;
        }
        $this->points = $points;
        $this->descriptions = $descriptions;
    }

    public function updateScore(float $oldScore, float $newScore)
    {
        //find the old and new pos
        $oldPos = -1;
        $newPos = -1;
        for($i = 0; $i < count($this->points); $i++)
        {
            if($newScore == $this->points[$i])
                return;
            if($this->points[$i] == $oldScore)
                $oldPos = $i;
            if($newScore > $this->points[$i] && $newPos == -1)
                $newPos = $i;
        }
        //are we staying in place?
        if($oldPos == $newPos)
        {
            //then just update the score.
            $this->points[$oldPos] = $newScore;
            return;
        }
        //re-order the points
        $points = [];
        for($i = 0; $i < count($this->points); $i++)
        {
            if($i == $oldPos)
                continue;
            if($i == $newPos)
                $points[] = $newScore;
            $points[] = $this->points[$i];
        }
        if($newPos == -1)
            $points[] = $newScore;
        //and we re-order the descriptions
        $descriptions = [];
        for($i = 0; $i < count($this->descriptions); $i++)
        {
            $descriptionRow = [];
            for($j = 0; $j < count($this->descriptions[$i]); $j++)
            {
                if($j == $oldPos)
                    continue;
                if($j == $newPos)
                    $descriptionRow[] = $this->descriptions[$i][$oldPos];
                $descriptionRow[] = $this->descriptions[$i][$j];
            }
            if($newPos == -1)
                $descriptionRow[] = $this->descriptions[$i][$oldPos];
            $descriptions[] = $descriptionRow;
        }
        $this->points = $points;
        $this->descriptions = $descriptions;
    }

    /**
     * METHOD IMPLEMENTATIONS
     */

    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        $data = json_decode($value, true);
        if($data && isset($data['criteria']))
            return Rubric::hydrate($data);
        return null;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return json_encode($value->toArray());
    }

    public function toArray()
    {
        return
        [
            'criteria' => $this->criteria,
            'points' => $this->points,
            'descriptions' => $this->descriptions,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }

    public static function hydrate(array $data): Rubric
    {
        $rubric = new Rubric();
        $rubric->criteria = $data['criteria'];
        $rubric->points = $data['points'];
        $rubric->descriptions = $data['descriptions'];
        return $rubric;
    }
}
