<?php

namespace App\Helpers\PostcodeLocationLoaders;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class MySocietyPostcodeLocationLoader implements PostcodeLocationLoader
{
    /** @var resource|null */
    private $csvFile = null;

    /** @var string[]|null */
    private ?array $headers;

    public function __construct(
        private string $url = 'https://parlvid.mysociety.org/os/ONSPD/2022-11.zip'
    ) {}

    private function downloadPostcodeZipFile(): string
    {
        $response = Http::get($this->url);

        if (!$response->successful()) {
            throw new Exception('Failed to download postcode zip file');
        }

        $fileName = implode('.', [
            getmypid(),
            rand(100000, 999999),
            'zip'
        ]);

        if (!Storage::disk('local')->put($fileName, $response->body())) {
            throw new Exception('Failed to save postcode zip file');
        }

        return Storage::disk('local')->path($fileName);
    }

    /**
     * @return resource
     * @throws Exception
     */
    private function getCsvFile()
    {
        if ($this->csvFile === null) {
            $zipFile = $this->downloadPostcodeZipFile();
            $zip = new ZipArchive();
            $zip->open($zipFile);
            $extractPath = substr($zipFile, 0, -4);
            $zip->extractTo($extractPath);
            $zip->close();

            $this->csvFile = fopen($extractPath . '/Data/ONSPD_NOV_2022_UK.csv', 'r');

            if (!$this->csvFile) {
                $this->csvFile = null;
                throw new Exception('Failed to open postcode csv file');
            }

            $this->headers = fgetcsv($this->csvFile);
        }

        return $this->csvFile;
    }

    protected function cleanup()
    {
        // TODO: Implement cleanup
    }

    /** @throws Exception */
    public function read(int $rows = 1): array
    {
        $data = [];

        while ($values = fgetcsv($this->getCsvFile())) {
            $row = array_combine($this->headers, $values);

            $data[] = [
                'postcode' => strtoupper(str_replace(' ', '', $row['pcd'])),
                'latitude' => $row['lat'],
                'longitude' => $row['long'],
            ];

            if (count($data) >= $rows) {
                break;
            }
        }

        if (empty($data)) {
            $this->cleanup();
        }

        return $data;
    }
}
