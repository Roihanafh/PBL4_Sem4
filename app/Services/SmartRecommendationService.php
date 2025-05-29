<?php

namespace App\Services;

class SmartRecommendationService
{
    protected array $weights = [
        'pref'   => 0.25,
        'skill'  => 0.25,
        'lokasi' => 0.20,
        'gaji'   => 0.20,
        'durasi' => 0.10,
    ];

    /**
     * @param array $data  
     *   Contoh: [
     *     ['id'=>1, 'preferensi'=>5, 'keahlian'=>4, 'lokasi'=>3, 'gaji'=>7, 'durasi'=>6],
     *     …  
     *   ]
     * @return array sorted list with ['id', 'score']
     */
    public function rank(array $data): array
    {
        // 1. Hitung min & max setiap kriteria
        $min = $max = [];
        foreach ($this->weights as $key => $_) {
            $values = array_column($data, $key);

            if (empty($values)) {
                // Default ke 0 agar tidak error
                $min[$key] = 0;
                $max[$key] = 1;
                continue;
            }

            $min[$key] = min($values);
            $max[$key] = max($values);
        }

        // 2. Normalisasi (utility) & hitung skor
        foreach ($data as &$item) {
            $utilities = [];
            foreach ($this->weights as $key => $w) {
                // Jika cost, pakai ($max[$key] - $item[$key]) / range; 
                // di sini semua diasumsikan benefit
                $range = $max[$key] - $min[$key] ?: 1;
                $utilities[$key] = ($item[$key] - $min[$key]) / $range;
            }
            // Agregasi weighted sum
            $score = 0;
            foreach ($utilities as $key => $u) {
                $score += $u * $this->weights[$key];
            }
            $item['score'] = round($score, 4);
        }
        unset($item);

        // 3. Urutkan descending
        usort($data, fn($a, $b) => $b['score'] <=> $a['score']);

        // 4. Kembalikan hanya id + score (atau seluruh item jika perlu)
        return array_map(fn($i) => ['id' => $i['id'], 'score' => $i['score']], $data);
    }
}
