<?php
declare(strict_types=1);

namespace console\controllers;

use common\models\User;
use common\rbac\EventOwnerRule;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\console\OutputInterface;
use yii\db\Query;

class RbacController extends Controller
{
    private const ROLES = ['sysAdmin', 'voluntario', 'empresa'];

    private const PERMISSIONS = [
        // --- Canais ---
        'accessBackoffice'  => 'Aceder a area de gestao (back-office)',
        'accessFrontoffice' => 'Aceder ao front-office autenticado',
        'accessMobile'      => 'Aceder a API da aplicacao mobile',

        // --- Evento e turnos ---
        'eventCreate'       => 'Criar um voluntariado/evento (nasce em DRAFT)',
        'eventManage'       => 'Gerir um evento que criei: editar, gerir os seus turnos, publicar, cancelar, concluir ou apagar',
        'moderateEvent'     => 'Back-office: gerir qualquer evento - publicar, cancelar, turnos e decidir candidaturas',

        // --- Candidaturas ---
        'applicationSubmit' => 'Candidatar-se a um evento',
        'applicationDecide' => 'Aprovar ou rejeitar candidaturas dos eventos que criei',
        // --- Presencas e horas ---
        'presenceConfirm'   => 'Registar horas e confirmar presencas nos turnos dos eventos que criei',
        // --- Utilizadores ---
        'userModerate'      => 'Back-office: gerir contas (ativar, suspender, rejeitar com motivo) e validar empresas',
        // --- Conteudo e sistema (reservadas para fase 2) ---
        'contentModerate'   => 'Reservada (fase 2): moderar conteudos e publicacoes',
        'systemManage'      => 'Reservada (fase 2): gerir as configuracoes do sistema',
    ];

    private const RULES_ON = ['eventManage', 'applicationDecide', 'presenceConfirm'];

    /**
     * Só a empresa cria e gere eventos.
     * O voluntário participa; o sysAdmin modera.
     */
    private const LINKS = [
        'sysAdmin'   => ['accessBackoffice', 'accessFrontoffice', 'moderateEvent', 'userModerate'],
        'voluntario' => ['accessFrontoffice', 'accessMobile', 'applicationSubmit'],
        'empresa'    => ['accessFrontoffice', 'eventCreate', 'eventManage',
            'applicationDecide', 'presenceConfirm'],
    ];

    /**
     * Links que existiam no modelo antigo e têm de ser removidos de instalações
     * onde o rbac/init já correu. O comando é idempotente.
     */
    private const OBSOLETE_LINKS = [
        'sysAdmin'   => ['accessMobile', 'contentModerate', 'systemManage'],
        'voluntario' => ['eventCreate', 'eventManage', 'applicationDecide', 'presenceConfirm'],
        'empresa'    => ['accessMobile'],
    ];

    /** php yii rbac/init  (idempotente) */
    public function actionInit(): int
    {
        $auth = Yii::$app->authManager;

        if ($auth->getRule('eventOwner') === null) {
            $auth->add(new EventOwnerRule());
            $this->stdout(" + regra eventOwner\n");
        }

        foreach (self::PERMISSIONS as $name => $description) {
            if ($auth->getPermission($name) === null) {
                $permission = $auth->createPermission($name);
                $permission->description = $description;
                $auth->add($permission);
                $this->stdout(" + permissao {$name}\n");
            }
        }

        foreach (self::ROLES as $roleName) {
            if ($auth->getRole($roleName) === null) {
                $auth->add($auth->createRole($roleName));
                $this->stdout(" + role {$roleName}\n");
            }
        }

        $rule = $auth->getRule('eventOwner');
        foreach (self::RULES_ON as $name) {
            $permission = $auth->getPermission($name);
            if ($permission->ruleName !== $rule->name) {
                $permission->ruleName = $rule->name;
                $auth->update($permission->name, $permission);
                $this->stdout(" ~ regra em {$name}\n");
            }
        }

        foreach (self::LINKS as $parentName => $children) {
            $parent = $auth->getRole($parentName);
            foreach ($children as $childName) {
                $child = $auth->getPermission($childName) ?: $auth->getRole($childName);
                if ($child !== null && !$auth->hasChild($parent, $child)) {
                    $auth->addChild($parent, $child);
                }
            }
        }

        // Remover links do modelo antigo (voluntário como coordenador)
        foreach (self::OBSOLETE_LINKS as $parentName => $children) {
            $parent = $auth->getRole($parentName);
            if ($parent === null) {
                continue;
            }
            foreach ($children as $childName) {
                $child = $auth->getPermission($childName);
                if ($child !== null && $auth->hasChild($parent, $child)) {
                    $auth->removeChild($parent, $child);
                    $this->stdout(" - link {$parentName} -> {$childName}\n");
                }
            }
        }

        $this->stdout(
            'OK - ' . count($auth->getRoles()) . ' roles, '
            . count($auth->getPermissions()) . " permissoes\n"
        );
        return ExitCode::OK;
    }

    /** php yii rbac/assign joao@mail.pt voluntario */
    public function actionAssign(string $email, string $roleName): int
    {
        $user = User::findOne(['email' => $email]);
        if ($user === null) {
            $this->stderr("Utilizador {$email} nao existe.\n", OutputInterface::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }

        if (!in_array($roleName, self::ROLES, true)) {
            $this->stderr('Role invalida. Disponiveis: ' . implode(', ', self::ROLES) . "\n", OutputInterface::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $auth = Yii::$app->authManager;
        if ($auth->getAssignment($roleName, $user->id) !== null) {
            $this->stdout("Ja atribuida.\n");
            return ExitCode::OK;
        }

        $auth->assign($auth->getRole($roleName), $user->id);
        $this->stdout("{$email} -> {$roleName}\n");
        return ExitCode::OK;
    }

    /** php yii rbac/revoke joao@mail.pt voluntario */
    public function actionRevoke(string $email, string $roleName): int
    {
        $user = User::findOne(['email' => $email]);
        if ($user === null) {
            $this->stderr("Utilizador {$email} nao existe.\n", OutputInterface::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $auth = Yii::$app->authManager;
        if ($auth->getAssignment($roleName, $user->id) === null) {
            $this->stdout("Nao tem esta role.\n");
            return ExitCode::OK;
        }

        Yii::$app->db->createCommand()->delete('{{%auth_assignment}}', [
            'item_name' => $roleName,
            'user_id'   => $user->id,
        ])->execute();

        $this->stdout("{$email} - role {$roleName} removida\n");
        return ExitCode::OK;
    }

    /** php yii rbac/check joao@mail.pt eventManage 5 */
    public function actionCheck(string $email, string $permissionName, string $eventId = ''): int
    {
        $user = User::findOne(['email' => $email]);
        if ($user === null) {
            $this->stderr("Utilizador {$email} nao existe.\n", OutputInterface::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $auth = Yii::$app->authManager;
        if ($auth->getPermission($permissionName) === null) {
            $this->stderr("Permissao {$permissionName} nao existe.\n", OutputInterface::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $params = [];
        if ($eventId !== '') {
            $event = (new Query())->from('{{%event}}')->where(['id' => (int) $eventId])->one();
            if ($event === null) {
                $this->stderr("Evento {$eventId} nao existe.\n", OutputInterface::FG_RED);
                return ExitCode::UNSPECIFIED_ERROR;
            }
            $params['event'] = $event;
        }

        $allowed = $auth->checkAccess($user->id, $permissionName, $params);

        $this->stdout(
            ($allowed ? 'PERMITIDO' : 'NEGADO')
            . " - {$email} :: {$permissionName}"
            . ($eventId !== '' ? " (evento {$eventId})" : '') . "\n"
        );
        return ExitCode::OK;
    }

    /** php yii rbac/list */
    public function actionList(): int
    {
        $auth = Yii::$app->authManager;

        foreach ($auth->getRoles() as $role) {
            $this->stdout("\n[{$role->name}]\n");
            foreach ($auth->getChildren($role->name) as $child) {
                $flag = $child->ruleName !== null ? '   (regra: ' . $child->ruleName . ')' : '';
                $this->stdout("   - {$child->name}{$flag}\n");
            }
        }

        return ExitCode::OK;
    }
}