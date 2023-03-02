<?php

namespace App\Http\Controllers\API;

use App\Aggregates\WalletAggregate;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateWalletRequest;
use App\Http\Requests\UpdateStatusWalletRequest;
use App\Http\Requests\UpdateWalletRequest;
use App\Http\Requests\WalletTransferRequest;
use App\Http\Resources\WalletCollection;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class WalletsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return WalletCollection
     */
    public function index(): WalletCollection
    {
        $wallets = Wallet::all();

        return new WalletCollection($wallets);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateWalletRequest  $request
     * @return JsonResponse
     */
    public function store(CreateWalletRequest $request): JsonResponse
    {
        $newUuid = Str::uuid()->toString();
        $user = Auth::user();
        User::checkWalletAlreadyExists($user);

        WalletAggregate::retrieve($newUuid)
            ->createWallet($user, $request->status)
            ->persist();

        return response()->json([
           'message' => 'Wallet Created Successfully'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateWalletRequest  $request
     * @param $id
     * @return JsonResponse
     */
    public function update(UpdateWalletRequest $request, $id)
    {
        $user = User::findOrFail($id);
        User::checkWalletExists($user);
        $wallet = $user->wallet;

        $aggregateRoot = WalletAggregate::retrieve($wallet->uuid);
        $isAdding = $request->adding();

        $isAdding
            ? $aggregateRoot->addMoney($request->amount)
            : $aggregateRoot->subtractMoney($request->amount);

        $aggregateRoot->persist();

        $msg = $isAdding
                    ? 'Amount added to the wallet successfully'
                    : 'Amount withdrawn form the wallet successfully';
        return response()->json([
            'message' => $msg
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $wallet = Wallet::findOrFail($id);
        WalletAggregate::retrieve($wallet->uuid)
                ->deleteWallet()
                ->persist();

        return response()->json([
            'message' => 'Wallet Deleted Successfully'
        ]);
    }

    /**
     * @param  WalletTransferRequest  $request
     * @return JsonResponse
     */
    public function transfer(WalletTransferRequest $request): JsonResponse
    {
        $source = Auth::user();
        $beneficiary = User::findOrFail($request->beneficiary);

        User::checkWalletExists($source);
        User::checkWalletExists($beneficiary, 'beneficiary');

        $sourceWallet = $source->wallet;
        $beneficiaryWallet = $beneficiary->wallet;
        $transferDetails = [
            'amount' => $request->amount,
            'transferred_to' => $request->beneficiary,
            'transferred_from' => $source->id
        ];

        $sourceAggregate = WalletAggregate::retrieve($sourceWallet->uuid)
                                ->subtractMoney($request->amount, $transferDetails);
        $beneficiaryAggregate = WalletAggregate::retrieve($beneficiaryWallet->uuid)
                                    ->addMoney($request->amount, $transferDetails);

        $sourceAggregate->persist();
        $beneficiaryAggregate->persist();

        return response()->json([
            'message' => 'Money Transferred Successfully'
        ]);
    }

    /**
     * @param  UpdateStatusWalletRequest  $request
     * @return JsonResponse
     */
    public function statusUpdate(UpdateStatusWalletRequest $request): JsonResponse
    {
        $user = User::findOrFail(Auth::id());

        User::checkWalletExists($user);

        $wallet = $user->wallet;
        WalletAggregate::retrieve($wallet->uuid)
            ->walletUpdate($request->status)
            ->persist();

        $msg = $request->status
            ? 'Wallet activated Successfully'
            : 'Wallet deactivated Successfully';
        return response()->json([
            'message' => $msg
        ]);
    }
}
