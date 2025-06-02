<?php

namespace App\Services;

class SmartRecommendationService
{
    protected array $weights = [
        'pref'   => 0.25,   // bobot preferensi kata kunci
        'skill'  => 0.25,   // bobot keahlian
        'lokasi' => 0.20,   // bobot lokasi
        'gaji'   => 0.20,   // bobot gaji
        'durasi' => 0.10,   // bobot durasi
    ];

    /**
     * @param array $data  
     *   Contoh elemen data:
     *     [
     *       'id'     => 1,
     *       'pref'   => 0.75,
     *       'skill'  => 0.50,
     *       'lokasi' => 0.50,
     *       'gaji'   => 3000000.0,   // akan dinormalisasi
     *       'durasi' => 0.80         // sudah di‐normalize 0..1
     *     ]
     * @return array sorted list dengan ['id', 'score']
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
                // Jika data sudah di‐normalize di luar (contoh durasi ∈ [0..1]),
                // maka kita tetap melakukan: (x - min)/(max - min).
                $range = ($max[$key] - $min[$key]) ?: 1;
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

        // 3. Urutkan descending berdasarkan skor
        usort($data, fn($a, $b) => $b['score'] <=> $a['score']);

        // 4. Kembalikan hanya id + score
        return array_map(fn($i) => [
            'id'    => $i['id'],
            'score' => $i['score']
        ], $data);
    }
}
