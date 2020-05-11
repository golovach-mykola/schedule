<?php


namespace App\Repositories;



use App\Schedule;
use Illuminate\Support\Collection;

class ScheduleRepository
{

    /**
     * Find what rallies can they move
     *
     * @param $date
     * @return Collection
     */
    public static function findRalliesCanMove($date) : Collection
    {
        $schedules = Schedule::where('date', $date)->with('user')->orderBy('time')->get();
        $schedules = $schedules->groupBy('time')->sort(function ($f1, $f2) {
            return (count($f1) < count($f2)) ? -1 : 1;
        });

        $collection = collect();
        foreach ($schedules as $key1 => $item1) {
            foreach ($schedules as $key2 => $item2) {
                if ($key1 != $key2 && count($item1) < count($item2)) {
                    $col = new \stdClass();
                    $col->time = $key2;
                    $col->schedules = $item1;
                    $collection->push($col);
                }
            }
        }
        return $collection;
    }

    /**
     * Rallies move to time
     *
     * @param Collection $list
     */
    public static function moveTime(Collection $list)
    {
        $list->schedules->map(function ($schedule) use ($list) {
            $schedule->time = $list->time;
            $schedule->save();
        });
    }
}
