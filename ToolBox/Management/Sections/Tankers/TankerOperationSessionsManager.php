<?php

namespace App\ToolBox\Management\Sections\Tankers;

use App\Helper\CacheHelper;
use App\TankerOperationSession;
use App\TankerOperationSessionStatus;
use App\ToolBox\Management\Tools\Manager;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use LogicException;
use RuntimeException;

class TankerOperationSessionsManager extends Manager
{
    /**
     * @param int $operationId
     * @param int $tankerId
     * @param int $tankerValue
     * @return TankerOperationSession
     */
    public static function start(int $operationId, int $tankerId, int $tankerValue): TankerOperationSession
    {
        DB::beginTransaction();
        try {
            self::resetTankerOperation($tankerId, $tankerValue);

            $session = new TankerOperationSession();

            $session->fill(
                [
                    TankerOperationSession::ATTRIBUTE_OPERATION_ID => $operationId,
                    TankerOperationSession::ATTRIBUTE_TANKER_ID    => $tankerId,
                    TankerOperationSession::ATTRIBUTE_START_WEIGHT => $tankerValue,
                    TankerOperationSession::ATTRIBUTE_STATUS_ID    => TankerOperationSessionStatus::STATUS_IN_PROCESS,
                    Model::UPDATED_AT                              => date("Y-m-d H:i:s"),
                ]
            )->save();

            DB::commit();
            return $session;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            throw new RuntimeException($e->getMessage());
        }
    }

    /**
     * @param int $sessionId
     * @param int $tankerValue
     * @return TankerOperationSession
     */
    public static function finish(int $sessionId, int $tankerValue): TankerOperationSession
    {
        /** @var TankerOperationSession $session */
        $session = self::getTankerOperationSession($sessionId);

        if ($session === null) {
            throw new LogicException("Session with id = $sessionId not found");
        }

        if ($session->getStatusId() !== TankerOperationSessionStatus::STATUS_IN_PROCESS) {
            throw new LogicException("Session with id = $sessionId is not in progress status");
        }

        DB::beginTransaction();
        try {
            $session->setStatusId(TankerOperationSessionStatus::STATUS_FINISHED)
                    ->setUpdatedAt(date("Y-m-d H:i:s"))
                    ->setEndWeight($tankerValue)
                    ->save();

            TankersManager::updateValue($session->getTankerId(), $tankerValue);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            throw new RuntimeException($e->getMessage());
        }

        return $session;
    }

    /**
     * @param int $tankerId
     * @return TankerOperationSession|null
     */
    public static function getCurrentTankerOperationSession(int $tankerId): ?TankerOperationSession
    {
        return CacheHelper::getOrSet("TankerCurrentOperation-$tankerId", TankerOperationSession::query()->where(
            [
                TankerOperationSession::ATTRIBUTE_TANKER_ID => $tankerId,
                TankerOperationSession::ATTRIBUTE_STATUS_ID => TankerOperationSessionStatus::STATUS_IN_PROCESS
            ])->limit(1)->get()->first());
    }

    /**
     * @param int $operationSessionId
     * @return mixed
     */
    public static function getTankerOperationSession(int $operationSessionId)
    {
        return CacheHelper::getOrSet("TankerOperation-$operationSessionId", TankerOperationSession::query()->where(
            [
                TankerOperationSession::ATTRIBUTE_ID => $operationSessionId,
            ])->limit(1)->get()->first());
    }

    # region helpers

    /**
     * @param int $tankerId
     * @param int $tankerValue
     */
    protected static function resetTankerOperation(int $tankerId, int $tankerValue)
    {
        $currentSession = self::getCurrentTankerOperationSession($tankerId);

        if (!empty($currentSession)) {
            $currentSession->setStatusId(TankerOperationSessionStatus::STATUS_FORCED_FINISH)
                           ->setUpdatedAt(date("Y-m-d H:i:s"))
                           ->setEndWeight($tankerValue)
                           ->save();
        }

        TankersManager::updateValue($tankerId, $tankerValue);
    }

    # endregion helpers
}
