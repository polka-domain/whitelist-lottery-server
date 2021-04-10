<?php

namespace App\Http\Controllers;

use App\Exceptions\BadRequestException;
use App\Helpers\EthereumValidator;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Pelieth\LaravelEcrecover\EthSigRecover;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response(User::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws BadRequestException
     */
    public function store(Request $request)
    {
        $request->validate([
            'eth_address' => 'required|unique:users,eth_address|size:42|starts_with:0x',
            'email' => 'required|email',
            'twitter' => 'required',
            'telegram' => 'required|starts_with:@',
            'domain' => 'required',
            'sign' => 'required|size:132|starts_with:0x',
            'token' => 'required',
        ]);

        // check address
        $ethValidator = new EthereumValidator();
        $address = $request->input('eth_address');
        if(!$ethValidator->isAddress($address)) {
            throw new BadRequestException('invalid_address');
        }

        // verify signature
        $eth_sig_util = new EthSigRecover();
        $sign = $request->input('sign');
        $recoverAddr = $eth_sig_util->personal_ecRecover($address, $sign);
        if (strtolower($address) !== strtolower($recoverAddr)) {
            throw new BadRequestException('invalid_signature');
        }

        // recapture
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
            'form_params' => [
                'secret' => '6LeEiqMaAAAAAGKfsE0MokVqbFVIEzvAHsz4R0Aq',
                'response' => $request->input('token'),
                'remoteip' => $request->ip(),
            ]
        ]);

        // check address
        $ethValidator = new EthereumValidator();
        $address = $request->input('eth_address');
        if(!$ethValidator->isAddress($address)) {
            throw new BadRequestException('invalid_address');
        }

        // verify signature
        $eth_sig_util = new EthSigRecover();
        $sign = $request->input('sign');
        $recoverAddr = $eth_sig_util->personal_ecRecover($address, $sign);
        if (strtolower($address) !== strtolower($recoverAddr)) {
            throw new BadRequestException('invalid_signature');
        }

        $user = User::create($request->only([
            'eth_address',
            'email',
            'twitter',
            'telegram',
            'domain',
        ]));

        return response($user);
    }

    /**
     * Display the specified resource.
     *
     * @param string $address
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
            throw new ModelNotFoundException();
        }

        return response($user);
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
