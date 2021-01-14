<?php

namespace app\job;

use app\common\model\Keywords;
use seo\Seo;
use think\queue\Job;

class Task
{
    public function fire(Job $job, $data)
    {
        file_put_contents('./task.txt', $data . '>>>>>开始:' . date('Y-m-d H:i:s') . '
', 8);
        $data = json_decode($data, true);
        if (!$this->checkData($data)) {
            $job->delete();
            print('Info: TaskA of Job MultiTask has been done and deleted' . '
');

        }
        $rank = $this->jobRankDone($data);
        $model = new Keywords;
        if (!empty($rank)) {
            try {
                $model->isUpdate(true)->save(['current_ranking' => $rank], ['id' => $data['id']]);
                $job->delete();
                echo '成功';

            } catch (\Exception $exception) {
                throw new \Exception($exception->getMessage());

            }

        } else {
            echo 'chongshi' . $job->attempts();
            if ($job->attempts() <= 10) {
                $job->release(60);

            } else {
                $model->isUpdate(true)->save(['current_ranking' => 102], ['id' => $data['oid']]);
                $job->delete();

            }

        }
    }

    public function failed($data)
    {

    }

    protected function checkData($data)
    {
        if (isset($data['debug']) && $data['debug']) {
            return false;

        }
        if (empty($data['taskid'])) {
            return false;

        }
        return true;

    }

    public function jobRankDone($data)
    {
        $current_ranking = '';
        $res = Seo::getBaidupc($data['taskid']);
        if ($res && $res['code'] == '0') {
            $ranks = $res['data']['keywordmonitor'][0]['ranks'];
            if (!empty($ranks)) {
                $current_ranking = $ranks[0]['rank'] ? $ranks[0]['rank'] : '';

            }

        } elseif ($res['code'] == '200104' && $res['msg'] == '数据采集中') {

        } else {

        }
        return $current_ranking;

    }
}