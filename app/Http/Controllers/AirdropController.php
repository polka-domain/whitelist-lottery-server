<?php

namespace App\Http\Controllers;

use App\Exceptions\BadRequestException;
use App\Helpers\EthereumValidator;
use App\Models\Airdrop;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Pelieth\LaravelEcrecover\EthSigRecover;

class AirdropController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws BadRequestException
     */
    public function show($address)
    {
        $ethValidator = new EthereumValidator();
        if(!$ethValidator->isAddress($address)) {
            throw new BadRequestException('invalid_address');
        }

        $user = User::findByAddress($address);
        if (!$user) {
            $user2 = Airdrop::findByAddress($address);
            if(!$user2) {
                throw new ModelNotFoundException();
            }
        }

        // sign message
        $eth_sig_util = new EthSigRecover();
        $client = new \GuzzleHttp\Client();
        $encoded = '0x000000000000000000000000' . substr($address, 2);
        $hashMessage = $eth_sig_util->keccak256(hex2bin(substr($encoded,2)));
        $signer = env('AIRDROP_SIGNER');
        $data = [
            'id' => 1,
            'jsonrpc' => '2.0',
            'method' => 'eth_sign',
            'params' => [$signer, $hashMessage],
        ];
        $res = $client->post(env('ETHEREUM_RPC'), [
            'json' => $data
        ]);
        $resJson = json_decode($res->getBody()->getContents(), true);
        if(array_key_exists('error', $resJson)) {
            throw new BadRequestException($resJson['error']['message']);
        }
        $signature = $resJson['result'];
        $v = hexdec(substr($signature, 130, 2));
        if ($v === 0) {
            $signature = substr($signature, 0, 130) . '1b' ; // 0x1b => 27
        } else if ($v === 1) {
            $signature = substr($signature, 0, 130) . '1c' ; // 0x1c => 28
        }

        return response([
            'address' => $address,
            'signature' => $signature,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
