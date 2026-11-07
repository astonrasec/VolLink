<?php

declare(strict_types=1);

namespace backend\mock;

/**
 * Dados de exemplo das páginas do back-office, tirados dos mockups.
 *
 * Servem enquanto os modelos ActiveRecord não existem: cada método devolve o que a
 * página real vai obter da base de dados. Ao criar os modelos, o controlador passa a
 * chamar a query e este ficheiro desaparece.
 */
final class MockData
{
    /**
     * Cartões do dashboard.
     *
     * @return list<array{label: string, value: string, icon: string, tone: string, url: array<int, string>}>
     */
    public static function stats(): array
    {
        return [
            ['label' => 'Utilizadores', 'value' => '128', 'icon' => 'users', 'tone' => 'mint', 'url' => ['/user/index']],
            ['label' => 'Empresas pendentes', 'value' => '6', 'icon' => 'building', 'tone' => 'amber', 'url' => ['/company/index']],
            ['label' => 'Eventos publicados', 'value' => '24', 'icon' => 'calendar-check', 'tone' => 'mint', 'url' => ['/event/index']],
            ['label' => 'Candidaturas', 'value' => '312', 'icon' => 'clipboard-list', 'tone' => 'mint', 'url' => ['/event/index']],
        ];
    }

    /**
     * Empresas à espera de validação (lista do dashboard).
     *
     * @return list<array{name: string, email: string, date: string, state: string}>
     */
    public static function pendingCompanies(): array
    {
        return [
            ['name' => 'Associação Horizonte', 'email' => 'geral@horizonte.pt', 'date' => '21/06/2025', 'state' => 'PENDING'],
            ['name' => 'Câmara Municipal de Leiria', 'email' => 'voluntariado@cm-leiria.pt', 'date' => '16/11/2026', 'state' => 'PENDING'],
            ['name' => 'Grupo Sol', 'email' => 'social@gruposol.pt', 'date' => '18/11/2026', 'state' => 'PENDING'],
        ];
    }

    /**
     * Últimos eventos (lista do dashboard).
     *
     * @return list<array{title: string, company: string, state: string, date: string}>
     */
    public static function recentEvents(): array
    {
        return array_map(
            static fn (array $event): array => [
                'title' => $event['title'],
                'company' => $event['company'],
                'state' => $event['state'],
                'date' => $event['date'],
            ],
            array_slice(self::events(), 0, 3),
        );
    }

    /**
     * Contas de voluntários e de empresas.
     *
     * @return list<array{id: int, name: string, email: string, type: string, state: string, registered: string, lastLogin: string}>
     */
    public static function users(): array
    {
        return [
            ['id' => 1, 'name' => 'Ana Silva', 'email' => 'ana.silva@email.pt', 'type' => 'Voluntário', 'state' => 'Ativo', 'registered' => '12/02/2025', 'lastLogin' => 'Hoje, 09:42'],
            ['id' => 2, 'name' => 'João Pereira', 'email' => 'joao.pereira@email.pt', 'type' => 'Voluntário', 'state' => 'Suspenso', 'registered' => '03/04/2025', 'lastLogin' => '02/11/2026'],
            ['id' => 3, 'name' => 'Maria Costa', 'email' => 'maria@horizonte.pt', 'type' => 'Empresa', 'state' => 'Ativo', 'registered' => '21/06/2025', 'lastLogin' => 'Ontem, 18:10'],
            ['id' => 4, 'name' => 'Pedro Santos', 'email' => 'pedro@gruposol.pt', 'type' => 'Empresa', 'state' => 'Pendente', 'registered' => '18/11/2026', 'lastLogin' => 'Nunca'],
            ['id' => 5, 'name' => 'Rita Nunes', 'email' => 'rita@refoodleiria.pt', 'type' => 'Empresa', 'state' => 'Ativo', 'registered' => '09/01/2026', 'lastLogin' => '15/11/2026'],
            ['id' => 6, 'name' => 'Carlos Mendes', 'email' => 'carlos.mendes@email.pt', 'type' => 'Voluntário', 'state' => 'Inativo', 'registered' => '27/09/2025', 'lastLogin' => '30/05/2026'],
        ];
    }

    /**
     * Contas de empresa e respetivo estado de validação.
     *
     * @return list<array{id: int, name: string, email: string, state: string, about: string, website: string, location: string, registered: string}>
     */
    public static function companies(): array
    {
        return [
            ['id' => 1, 'name' => 'Associação Horizonte', 'email' => 'geral@horizonte.pt', 'state' => 'APPROVED', 'about' => 'Promove a inclusão social e o envolvimento da comunidade através de projetos de voluntariado.', 'website' => 'www.horizonte.pt', 'location' => 'Leiria', 'registered' => '21/06/2025'],
            ['id' => 2, 'name' => 'Câmara Municipal de Leiria', 'email' => 'voluntariado@cm-leiria.pt', 'state' => 'PENDING', 'about' => 'Entidade municipal responsável por iniciativas locais de voluntariado.', 'website' => 'www.cm-leiria.pt', 'location' => 'Leiria', 'registered' => '16/11/2026'],
            ['id' => 3, 'name' => 'Grupo Sol', 'email' => 'social@gruposol.pt', 'state' => 'PENDING', 'about' => 'Grupo de jovens dedicado a ações ambientais e de apoio à comunidade.', 'website' => 'www.gruposol.pt', 'location' => 'Marinha Grande', 'registered' => '18/11/2026'],
            ['id' => 4, 'name' => 'Refood Leiria', 'email' => 'leiria@re-food.org', 'state' => 'REJECTED', 'about' => 'Recolha e distribuição de refeições excedentárias.', 'website' => 'www.re-food.org', 'location' => 'Leiria', 'registered' => '02/10/2026'],
            ['id' => 5, 'name' => 'Verde Futuro', 'email' => 'geral@verdefuturo.pt', 'state' => 'SUSPENDED', 'about' => 'Plantação de árvores e limpeza de espaços naturais.', 'website' => 'www.verdefuturo.pt', 'location' => 'Batalha', 'registered' => '14/03/2026'],
        ];
    }

    /**
     * Eventos da plataforma, com o detalhe mostrado no painel lateral.
     *
     * @return list<array{id: int, title: string, company: string, state: string, date: string, description: string, category: string, location: string, start: string, end: string, deadline: string, capacity: int, applications: int, shifts: list<array{name: string, time: string, seats: int}>}>
     */
    public static function events(): array
    {
        $shifts = [
            ['name' => 'Turno da manhã', 'time' => '09:00 — 13:00', 'seats' => 24],
            ['name' => 'Turno da tarde', 'time' => '14:00 — 18:00', 'seats' => 26],
        ];

        return [
            ['id' => 1, 'title' => 'Limpeza da praia', 'company' => 'Associação Horizonte', 'state' => 'Publicado', 'date' => '24/11/2026', 'description' => 'Atividade de voluntariado aberta à comunidade, organizada por Associação Horizonte.', 'category' => 'Apoio comunitário', 'location' => 'Leiria', 'start' => '24/11/2026, 09:00', 'end' => '24/11/2026, 13:00', 'deadline' => '22/11/2026', 'capacity' => 50, 'applications' => 42, 'shifts' => $shifts],
            ['id' => 2, 'title' => 'Banco alimentar', 'company' => 'Câmara Municipal de Leiria', 'state' => 'Rascunho', 'date' => '28/11/2026', 'description' => 'Recolha e triagem de bens alimentares para as famílias do concelho.', 'category' => 'Apoio social', 'location' => 'Leiria', 'start' => '28/11/2026, 14:00', 'end' => '28/11/2026, 18:00', 'deadline' => '26/11/2026', 'capacity' => 30, 'applications' => 0, 'shifts' => array_slice($shifts, 1)],
            ['id' => 3, 'title' => 'Plantação de árvores', 'company' => 'Grupo Sol', 'state' => 'Cancelado', 'date' => '02/12/2026', 'description' => 'Plantação de espécies autóctones numa zona ardida.', 'category' => 'Ambiente', 'location' => 'Marinha Grande', 'start' => '02/12/2026, 09:30', 'end' => '02/12/2026, 12:30', 'deadline' => '30/11/2026', 'capacity' => 40, 'applications' => 12, 'shifts' => array_slice($shifts, 0, 1)],
            ['id' => 4, 'title' => 'Apoio a idosos', 'company' => 'Associação Horizonte', 'state' => 'Concluído', 'date' => '10/11/2026', 'description' => 'Visitas e companhia a utentes de um lar de idosos.', 'category' => 'Apoio social', 'location' => 'Leiria', 'start' => '10/11/2026, 10:00', 'end' => '10/11/2026, 12:00', 'deadline' => '08/11/2026', 'capacity' => 20, 'applications' => 20, 'shifts' => array_slice($shifts, 0, 1)],
            ['id' => 5, 'title' => 'Recolha de roupa', 'company' => 'Refood Leiria', 'state' => 'Arquivado', 'date' => '05/10/2026', 'description' => 'Campanha de recolha de roupa e agasalhos.', 'category' => 'Apoio social', 'location' => 'Leiria', 'start' => '05/10/2026, 10:00', 'end' => '05/10/2026, 16:00', 'deadline' => '03/10/2026', 'capacity' => 15, 'applications' => 9, 'shifts' => array_slice($shifts, 0, 1)],
            ['id' => 6, 'title' => 'Workshop de CV', 'company' => 'Câmara Municipal de Leiria', 'state' => 'Publicado', 'date' => '12/12/2026', 'description' => 'Sessão prática de revisão de currículos para jovens à procura de emprego.', 'category' => 'Educação', 'location' => 'Leiria', 'start' => '12/12/2026, 15:00', 'end' => '12/12/2026, 17:00', 'deadline' => '10/12/2026', 'capacity' => 25, 'applications' => 14, 'shifts' => array_slice($shifts, 1)],
        ];
    }
}
