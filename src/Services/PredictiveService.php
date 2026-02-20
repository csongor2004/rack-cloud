<?php
namespace App\Services;

class PredictiveService
{
    public function estimateDaysUntilFull($history, $limit)
    {
        if (count($history) < 2)
            return "Nincs elég adat a becsléshez";

        $n = count($history);
        $sumX = $sumY = $sumXY = $sumX2 = 0;

        foreach ($history as $i => $data) {
            $x = $i; // napok indexe
            $y = $data['cumulative_size'];
            $sumX += $x;
            $sumY += $y;
            $sumXY += ($x * $y);
            $sumX2 += ($x * $x);
        }

        // Meredekség (m) kiszámítása
        $m = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
        if ($m <= 0)
            return "A tárhelyhasználat stagnál.";

        $currentSize = end($history)['cumulative_size'];
        $remainingSpace = $limit - $currentSize;

        return ceil($remainingSpace / $m); // Megmaradt napok száma
    }
}