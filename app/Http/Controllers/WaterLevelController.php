<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WaterLevel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WaterLevelController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function getWaterLevel()
    {
        $latestWaterLevel = WaterLevel::latest()->first();

        if (!$latestWaterLevel) {
            return response()->json([
                'level' => null,
                'status' => 'No data available'
            ]);
        }

        $level = $latestWaterLevel->level;

        $status = $this->getLevelStatus($level);

        return response()->json([
            'level' => $level,
            'status' => $status
        ]);
    }

    public function store(Request $request)
    {
        \Log::info('Request Data: ', $request->all());

        $request->validate([
            'level' => 'required|numeric'
        ]);

        $waterLevel = new WaterLevel();
        $waterLevel->level = $request->level;
        $waterLevel->created_at = Carbon::now('Asia/Jakarta'); // Store time in WIB
        $waterLevel->save();

        // Mengirim notifikasi berdasarkan kondisi level
        $this->checkAndSendNotification($waterLevel);

        return response()->json([
            'message' => 'Water level recorded successfully',
            'data' => $waterLevel
        ]);
    }

    public function getWaterLevelData()
    {
        $waterLevels = WaterLevel::select('level', DB::raw('DATE_FORMAT(created_at, "%H:%i:%s") as time'))
            ->orderBy('created_at', 'desc')
            ->take(15)
            ->get()
            ->sortBy('created_at'); // Sorting back to ascending order for proper display

        return response()->json($waterLevels);
    }

    public function history(Request $request)
    {
        $query = WaterLevel::orderBy('created_at', 'desc');
    
        // Apply filters if provided
        if ($request->has('date') && $request->input('date')) {
            $date = Carbon::parse($request->input('date'))->format('Y-m-d');
            $query->whereDate('created_at', $date);
        }
    
        if ($request->has('start_time') && $request->input('start_time')) {
            $startTime = Carbon::parse($request->input('start_time'))->format('H:i:s');
            $query->whereTime('created_at', '>=', $startTime);
        }
    
        if ($request->has('end_time') && $request->input('end_time')) {
            $endTime = Carbon::parse($request->input('end_time'))->format('H:i:s');
            $query->whereTime('created_at', '<=', $endTime);
        }
    
        if ($request->has('time') && $request->input('time')) {
            $time = $request->input('time');
            $query->whereTime('created_at', '=', $time);
        }
    
        $allLevels = $query->get();
    
        // Transform all levels to include additional fields
        $allLevels->transform(function ($waterLevel, $key) {
            $ketinggianAir = 84 - $waterLevel->level; // Menghitung ketinggian air
            $volume = $this->calculateVolume($ketinggianAir); // Menghitung volume
    
            $waterLevel->no = $key + 1;
            $waterLevel->tanggal = Carbon::parse($waterLevel->created_at)->format('Y-m-d');
            $waterLevel->waktu = Carbon::parse($waterLevel->created_at)->timezone('Asia/Jakarta')->format('H:i:s');
            $waterLevel->ketinggian_air = round($ketinggianAir, 2); // Menyimpan hasil ketinggian air
            $waterLevel->volume = round($volume, 2); // Menyimpan hasil volume
            $waterLevel->status = $this->getLevelStatus($waterLevel->level);
            return $waterLevel;
        });
    
        // Take first 10 for display (latest first)
        $displayedLevels = $allLevels->take(10);
    
        return view('history', [
            'displayedLevels' => $displayedLevels,
            'allLevels' => $allLevels
        ]);
    }
    

    private function getLevelStatus($level)
    {
        $maxHeight = 84; // Tinggi maksimum sumur dalam meter
    
        if ($level < 0.40 * $maxHeight) {
            return "AMAN"; // H < 33.6 meter
        } elseif ($level < 0.60 * $maxHeight) {
            return "RAWAN"; // 33.6 ≤ H < 50.4 meter
        } elseif ($level < 0.80 * $maxHeight) {
            return "KRITIS"; // 50.4 ≤ H < 67.2 meter
        } else {
            return "RUSAK"; // H ≥ 67.2 meter
        }
    }
    
    private function checkAndSendNotification($waterLevel)
    {
        $level = $waterLevel->level;
        $maxHeight = 84; // Tinggi maksimum sumur dalam meter
    
        if ($level >= 0.60 * $maxHeight && $level < 0.80 * $maxHeight) {
            // Notifikasi untuk level KRITIS
            $message = "[PERHATIAN] Ketinggian Air Kritis\nSumur PAM Sagara di Desa Sindangkerta\n\nKetinggian air sumur telah mencapai level kritis:\n- Ketinggian Air: {$level} meter\n\nMohon segera cek kondisi sumur untuk memastikan pasokan air tetap tersedia.\n\nTerima kasih.";
            $this->sendNotificationMultipleTimes($message, 1);
        } elseif ($level >= 0.80 * $maxHeight) {
            // Notifikasi untuk level RUSAK
            $message = "[PERHATIAN] Ketinggian Air RUSAK\nSumur PAM Sagara di Desa Sindangkerta\n\nKetinggian air sumur telah mencapai level rusak:\n- Ketinggian Air: {$level} meter\n\nMohon segera ambil langkah yang telah dipersiapkan karena sumur sudah tidak layak digunakan.\n\nTerima kasih.";
            $this->sendNotificationMultipleTimes($message, 1);
        }
    }


    private function sendNotificationMultipleTimes($message, $times)
    {
        for ($i = 0; $i < $times; $i++) {
            $this->sendWhatsAppNotification($message);
        }
    }

    private function sendWhatsAppNotification($message)
    {
        $apiKey = 'MzsBRot-qygPthpnnvmE'; // Ganti dengan API key Fonnte Anda
        $phoneNumber = '089622116268'; // Nomor WhatsApp tujuan

        // Prepare data for WhatsApp notification
        $postData = json_encode([
            'target' => $phoneNumber,
            'message' => $message,
            'countryCode' => '62' // Optional: adjust according to your needs
        ]);

        // Send WhatsApp notification using cURL
        $curl = curl_init();

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => 'https://api.fonnte.com/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $postData,
                CURLOPT_HTTPHEADER => array(
                    'Authorization: ' . $apiKey,
                    'Content-Type: application/json'
                ),
            )
        );

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            \Log::error('Error sending WhatsApp notification: ' . $err);
        } else {
            \Log::info('WhatsApp notification sent successfully. Response: ' . $response);
        }
    }
    public function downloadReport(Request $request)
{
    $startDate = $request->query('start_date');
    $endDate = $request->query('end_date');

    if (!$startDate || !$endDate) {
        return redirect()->back()->withErrors('Please provide both start and end dates.');
    }

    $startDate = Carbon::parse($startDate)->startOfDay();
    $endDate = Carbon::parse($endDate)->endOfDay();

    $data = WaterLevel::whereBetween('created_at', [$startDate, $endDate])
        ->orderBy('created_at', 'asc')
        ->get();

    $fileName = 'report-' . Carbon::now()->format('Y-m-d') . '.csv';

    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename=\"$fileName\"",
    ];

    $columns = ['No', 'Tanggal', 'Waktu', 'Jarak', 'Status'];

    $callback = function() use ($data, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($data as $index => $record) {
            $row = [
                $index + 1,
                Carbon::parse($record->created_at)->format('Y-m-d'),
                Carbon::parse($record->created_at)->format('H:i:s'),
                $record->level . ' Meter',
                $this->getLevelStatus($record->level),
            ];
            fputcsv($file, $row);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
public function getChartData(Request $request)
{
    $startDate = $request->query('start_date');
    $endDate = $request->query('end_date');

    if (!$startDate || !$endDate) {
        return response()->json(['error' => 'Please provide both start and end dates.'], 400);
    }

    $startDate = Carbon::parse($startDate)->startOfDay();
    $endDate = Carbon::parse($endDate)->endOfDay();

    $data = WaterLevel::select(DB::raw('DATE(created_at) as date'), DB::raw('AVG(level) as average_level'))
        ->whereBetween('created_at', [$startDate, $endDate])
        ->groupBy(DB::raw('DATE(created_at)'))
        ->orderBy('date')
        ->get()
        ->map(function ($item) {
            return [
                'date' => Carbon::parse($item->date)->format('Y-m-d'),
                'average_level' => $item->average_level,
            ];
        });

    return response()->json($data);
}
private function calculateVolume($height)
{
    $radius = 0.0825; // 82.5 mm diubah menjadi meter
    $volumeInCubicMeters = pi() * pow($radius, 2) * $height; // Volume dalam meter kubik
    $volumeInLiters = $volumeInCubicMeters * 1000; // Ubah ke liter
    return $volumeInLiters;
}

}
