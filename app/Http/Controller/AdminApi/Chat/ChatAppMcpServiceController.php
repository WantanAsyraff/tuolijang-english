<?php

declare(strict_types=1);


namespace App\Http\Controller\AdminApi\Chat;

use App\Http\Controller\AdminApi\AuthController;
use App\Http\Requests\Chat\ChatAppMcpServiceRequest;
use App\Http\Service\Chat\ChatAppMcpServiceService;
use App\Mcp\ExternalMcpClient;
use crmeb\interfaces\ResourceControllerInterface;
use crmeb\traits\ResourceControllerTrait;
use Spatie\RouteAttributes\Attributes\Delete;
use Spatie\RouteAttributes\Attributes\Middleware;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Put;
use Spatie\RouteAttributes\Attributes\Resource;

/**
 * 聊天应用MCP服务配置管理.
 */
#[Prefix('ent/chat/mcp')]
#[Resource('/', false, except: ['create'], names: [
    'index'   => 'MCP服务列表',
    'store'   => '保存MCP服务',
    'show'    => '修改MCP服务状态',
    'edit'    => '获取MCP服务',
    'update'  => '修改MCP服务',
    'destroy' => '删除MCP服务',
], parameters: ['' => 'id'])]
#[Middleware(['auth.admin', 'ent.auth', 'ent.log'])]
class ChatAppMcpServiceController extends AuthController implements ResourceControllerInterface
{
    use ResourceControllerTrait;

    public function __construct(ChatAppMcpServiceService $services)
    {
        parent::__construct();
        $this->service = $services;
    }

    /**
     * 删除MCP服务（系统默认不可删除）.
     */
    #[Delete('/{id}', '删除MCP服务')]
    public function destroy($id)
    {
        $record = $this->service->get((int) $id);
        if (! $record) {
            return $this->fail('数据不存在');
        }
        if ($record->is_default) {
            return $this->fail('系统默认MCP服务不可删除');
        }

        return $this->service->resourceDelete((int) $id) ? $this->success('删除成功') : $this->fail('删除失败');
    }

    /**
     * 修改MCP服务（系统默认仅允许修改状态和排序）.
     */
    #[Put('/{id}', '修改MCP服务')]
    public function update($id)
    {
        $record = $this->service->get((int) $id);
        if (! $record) {
            return $this->fail('数据不存在');
        }

        $data = $this->request->postMore($this->getRequestFields());


        return $this->service->resourceUpdate((int) $id, $data) ? $this->success('修改成功') : $this->fail('修改失败');
    }

    /**
     * 测试MCP服务连接.
     */
    #[Post('test-connection', '测试MCP连接')]
    public function testConnection()
    {
        $data = $this->request->postMore([
            ['service_url', ''],
            ['headers', ''],
        ]);
        if (empty($data['service_url'])) {
            return $this->fail('服务地址不能为空');
        }
        $headers = $data['headers'] ? (json_decode($data['headers'], true) ?: []) : [];
        $client  = new ExternalMcpClient($data['service_url'], $headers);
        $result  = $client->testConnection();

        return $result['success'] ? $this->success($result) : $this->fail($result['message']);
    }

    protected function getSearchField(): array
    {
        return [
            ['name', ''],
            ['status', ''],
        ];
    }

    protected function getRequestClassName(): string
    {
        return ChatAppMcpServiceRequest::class;
    }

    protected function getRequestFields(): array
    {
        return [
            ['name', ''],
            ['info', ''],
            ['type', 'sse'],
            ['service_url', ''],
            ['headers', ''],
            ['config_json', ''],
            ['status', 1],
            ['sort', 0],
            ['is_default', 0],
        ];
    }
}
