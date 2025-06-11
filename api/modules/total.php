<?php
header("Content-Type: application/json");
error_reporting(1);
date_default_timezone_set("Asia/Jakarta");

class TransactionService
{
    private string $baseUrl = "https://192.168.1.50:8098/api/transaction/list";
    private string $accessToken = "97F30B9A7890AFBD347C53322B0C5653";
    private array $inDevices = ["TRIPOD-IN-POS-1", "MOTOR-IN-POS-1", "MOBIL-IN-1-POS-1", "MOBIL-IN-2-POS-1"];
    private array $outDevices = ["TRIPOD-OUT-POS-1", "MOTOR-OUT-POS-1", "MOBIL-OUT-1-POS-1", "MOBIL-OUT-2-POS-1"];

    public function getSummary(): array
    {
        $totalIn = 0;
        $totalOut = 0;
        $totalCur = 0;
        $page = 1;

        while (true) {
            $data = $this->fetchTransactionPage($page);
            if (empty($data['data'])) {
                break;
            }

            $counts = $this->countPageDevices($data['data']);

            foreach ($counts as $c) {
                $totalIn += $c['in'];
                $totalOut += $c['out'];
                $totalCur += ($c['in'] - $c['out']);
            }

            $page++;
        }

        return [
            'data' => [
                [
                    'totalin' => $totalIn,
                    'totalout' => $totalOut,
                    'totalcur' => $totalCur,
                ]
            ]
        ];
    }

    private function countPageDevices(array $events): array
    {
        $counts = [];

        foreach ($events as $event) {
            $dept = $event['deptName'] ?? '';
            $devName = $event['devName'] ?? '';
            $eventName = $event['eventName'] ?? '';

            // Skip event tertentu & dept kosong
            if (in_array($eventName, ['Global Anti-Passback(logical)', 'Disconnected']) || empty($dept)) {
                continue;
            }

            if (!isset($counts[$dept])) {
                $counts[$dept] = ['in' => 0, 'out' => 0];
            }

            if ($devName !== '') {
                foreach ($this->inDevices as $inDev) {
                    if (stripos($devName, $inDev) !== false) {
                        $counts[$dept]['in']++;
                        continue 2; // lanjut event selanjutnya
                    }
                }

                foreach ($this->outDevices as $outDev) {
                    if (stripos($devName, $outDev) !== false) {
                        $counts[$dept]['out']++;
                        continue 2;
                    }
                }
            }
        }

        return $counts;
    }

    private function fetchTransactionPage(int $page): ?array
    {
        $startDate = date('y-m-d');
        $endDate = date('y-m-d', strtotime("+1 day"));

        $url = "{$this->baseUrl}?endDate=20{$endDate}%2023%3A59%3A59&pageNo={$page}&pageSize=800&startDate=20{$startDate}%2000%3A00%3A00&access_token={$this->accessToken}";

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => [
                'Content-Type:application/json',
                'Accept:application/json'
            ],
        ]);

        $response = curl_exec($ch);

        if ($response === false) {
            // Bisa tambahkan log error jika diperlukan
            curl_close($ch);
            return null;
        }

        curl_close($ch);

        return json_decode($response, true);
    }
}

// Eksekusi
$service = new TransactionService();
echo json_encode($service->getSummary(), JSON_PRETTY_PRINT);
?>