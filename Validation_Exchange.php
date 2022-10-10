<?php

namespace App\Http\Controllers\Exchange;

use App\DTO\input\PrebidSourceDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\ScanLimitsUpdateRequest;
use App\Models\BaseModel;
use App\Models\BidResponse;
use App\Models\CheckTrafSSPExclude;
use App\Models\DspSettingsPrebidSourceMapper;
use App\Models\ListCustomSSP;
use App\Models\ListDSP;
use App\Models\ListSSP;
use App\Models\PrebidjsStats;
use App\Models\PrebidPartners;
use App\Models\Project;
use App\Models\ScanLimits;
use App\Models\SettingsDSP;
use App\Models\SettingsDspPrebid;
use App\Models\SettingsEmailReport;
use App\Models\SettingsScanDiscrepReport;
use App\Models\SettingsSSP;
use App\Models\SspPlacementsSettings;
use App\Services\CustomSspService;
use App\Services\SspPlacementService;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use JsonSchema\Constraints\Constraint;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ExchangeController extends Controller
{

    public function __construct() {}

    public function dspSourcesCreate(Request $request) {

        $requestDTO = PrebidSourceDTO::fromRequest($request);
        $dspSettingsPrebidSourceMapper = new DspSettingsPrebidSourceMapper();

        $dspCompanyName = $requestDTO->dspCompanyName ?? '';
        $source = $requestDTO->source ?? '';
        $partner = $requestDTO->partner ?? '';
        $prebid = $requestDTO->prebid ?? [];

        if (empty($requestDTO->dspCompanyName)) {
            $errors[] = 'Company name is required';
        } elseif (empty($requestDTO->source)) {
            $errors[] = 'Source name is required';
        } elseif (empty($requestDTO->partner)) {
            $errors[] = 'Partner name is required';
        }


        $uniqueDspSource = $dspSettingsPrebidSourceMapper
            ->where('source', '=', $source)
            ->where('dspCompanyName', '=', $dspCompanyName)
            ->first();

        if ($uniqueDspSource) {
            $errors[] = 'Dsp Source already exists!';
        }

        if (empty($errors)) {

            try {
                $prebidPartnerInfo = (new PrebidPartners)->getPartnerInfo($partner);

                $inputJson = !empty($requestDTO->prebid) ? json_decode(json_encode($requestDTO->prebid)) : (object)[];
                $partnerJsonSchema = $prebidPartnerInfo->params ?? [];

                $validator = new \JsonSchema\Validator();
                $validator->validate($inputJson, $partnerJsonSchema, Constraint::CHECK_MODE_EXCEPTIONS);

                if ($validator->isValid()) {
                    $prebidExt = [];
                    $prebidExt[$partner] = $prebid;

                    $dspSettingsPrebidSourceMapper->dspCompanyName = $dspCompanyName;
                    $dspSettingsPrebidSourceMapper->source = $source;
                    $dspSettingsPrebidSourceMapper->prebidExt = json_encode($prebidExt);
                    $dspSettingsPrebidSourceMapper->save();
                }

            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'errors' => [$e->getMessage()]
                ], 400);
            }

            return response()->json([
                'status' => 'success',
            ]);

        } else {

            return response()->json([
                'status' => 'error',
                'errors' => array_unique($errors)
            ], 400);
        }
    }

    public function dspSourcesDelete(Request $request) {

        $requestDTO = PrebidSourceDTO::fromRequest($request);
        $dspSettingsPrebidSourceMapper = new DspSettingsPrebidSourceMapper();

        $dspSource = $dspSettingsPrebidSourceMapper
            ->where('source', '=', $requestDTO->source)
            ->where('dspCompanyName', '=', $requestDTO->dspCompanyName)
            ->first();

        if (!$dspSource) {
            return response()->json([
                'status' => 'error',
                'errors' => ['Unknown Dsp Source']
            ], 400);
        }

        if ($dspSettingsPrebidSourceMapper->where([
            ['source', '=', $requestDTO->source],
            ['dspCompanyName', '=', $requestDTO->dspCompanyName],
        ])->delete()) {
            return response()->json([
                'status' => 'success',
            ]);
        }

        return response()->json([
            'status' => 'error',
            'errors' => ['Dsp Source delete error']
        ], 400);
    }

}
