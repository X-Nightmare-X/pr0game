<?php

class StatPoints
{
    public int $id_owner = 0;
    public int $id_ally = 0;
    public int $stat_type = 0;
    public int $universe = 0;
    public int $tech_rank = 0;
    public int $tech_old_rank = 0;
    public int $tech_points = 0;
    public int $tech_count = 0;
    public int $build_rank = 0;
    public int $build_old_rank = 0;
    public int $build_points = 0;
    public int $build_count = 0;
    public int $defs_rank = 0;
    public int $defs_old_rank = 0;
    public int $defs_points = 0;
    public int $defs_count = 0;
    public int $fleet_rank = 0;
    public int $fleet_old_rank = 0;
    public int $fleet_points = 0;
    public int $fleet_count = 0;
    public int $total_rank = 0;
    public int $total_old_rank = 0;
    public int $total_points = 0;
    public int $total_count = 0;

    public function __construct(int $idOwner, int $idAlliance, int $statType, int $universe)
    {
        $this->id_owner = $idOwner;
        $this->id_ally = $idAlliance;
        $this->stat_type = $statType;
        $this->universe = $universe;
    }

    public function setTotalStats()
    {
        $this->total_count = $this->tech_count + $this->build_count + $this->fleet_count + $this->defs_count;
        $this->total_points = $this->tech_points + $this->build_points + $this->fleet_points + $this->defs_points;
    }
}
