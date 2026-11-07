<?php

declare(strict_types=1);

namespace frontend\mock;

/**
 * Dados de exemplo das páginas do front-office, tirados dos mockups.
 *
 * Servem enquanto os modelos ActiveRecord não existem: cada método devolve o que a
 * página real vai obter da base de dados. Ao criar os modelos, o controlador passa a
 * chamar a query e este ficheiro desaparece.
 */
final class MockData
{
    /**
     * Cor e ícone de cada categoria de evento.
     *
     * @return array<string, array{tone: string, icon: string}>
     */
    public static function categories(): array
    {
        return [
            'Ambiente' => ['tone' => 'green', 'icon' => 'leaf'],
            'Social' => ['tone' => 'orange', 'icon' => 'users'],
            'Educação' => ['tone' => 'blue', 'icon' => 'book-open'],
        ];
    }

    /**
     * Eventos do feed (os três primeiros do catálogo).
     *
     * @return list<array<string, mixed>>
     */
    public static function events(): array
    {
        return array_slice(self::catalogue(), 0, 3);
    }

    /**
     * Eventos publicados pela empresa do perfil público.
     *
     * @return list<array<string, mixed>>
     */
    public static function companyPublishedEvents(): array
    {
        $catalogue = self::catalogue();

        return [$catalogue[0], $catalogue[3]];
    }

    /**
     * Todos os eventos de exemplo.
     *
     * @return list<array<string, mixed>>
     */
    private static function catalogue(): array
    {
        return [
            [
                'id' => 1, 'title' => 'Limpeza da praia', 'category' => 'Ambiente',
                'company' => 'Associação Horizonte', 'verified' => true,
                'date' => '24/11/2026', 'time' => '09:00–13:00', 'location' => 'Leiria',
                'seats' => 50, 'applications' => 42, 'deadline' => '22/11/2026', 'state' => 'Publicado',
                'about' => 'Uma atividade aberta à comunidade, com acompanhamento da organização e todo o material necessário disponibilizado no local.',
                'myApplication' => 'Aprovada',
                'shifts' => [
                    ['name' => 'Turno da manhã', 'time' => '09:00–13:00', 'seats' => 24],
                    ['name' => 'Turno da tarde', 'time' => '14:00–18:00', 'seats' => 26],
                ],
            ],
            [
                'id' => 2, 'title' => 'Banco alimentar', 'category' => 'Social',
                'company' => 'Câmara Municipal de Leiria', 'verified' => false,
                'date' => '28/11/2026', 'time' => '14:00–18:00', 'location' => 'Leiria',
                'seats' => 30, 'applications' => 18, 'deadline' => '26/11/2026', 'state' => 'Publicado',
                'about' => 'Recolha e triagem de bens alimentares para as famílias do concelho, com formação inicial no local.',
                'myApplication' => 'Pendente',
                'shifts' => [
                    ['name' => 'Turno único', 'time' => '14:00–18:00', 'seats' => 30],
                ],
            ],
            [
                'id' => 3, 'title' => 'Workshop de CV', 'category' => 'Educação',
                'company' => 'Câmara Municipal de Leiria', 'verified' => false,
                'date' => '12/12/2026', 'time' => '15:00–17:00', 'location' => 'Leiria',
                'seats' => 25, 'applications' => 14, 'deadline' => '10/12/2026', 'state' => 'Publicado',
                'about' => 'Sessão prática de revisão de currículos para jovens à procura do primeiro emprego.',
                'myApplication' => 'Rejeitada',
                'shifts' => [
                    ['name' => 'Sessão única', 'time' => '15:00–17:00', 'seats' => 25],
                ],
            ],
            [
                'id' => 4, 'title' => 'Apoio a idosos', 'category' => 'Social',
                'company' => 'Associação Horizonte', 'verified' => true,
                'date' => '10/10/2026', 'time' => '10:00–12:00', 'location' => 'Leiria',
                'seats' => 20, 'applications' => 20, 'deadline' => '08/10/2026', 'state' => 'Concluído',
                'about' => 'Visitas e companhia a utentes de um lar de idosos.',
                'myApplication' => 'Aprovada',
                'shifts' => [
                    ['name' => 'Turno único', 'time' => '10:00–12:00', 'seats' => 20],
                ],
            ],
        ];
    }

    /**
     * Um evento pelo id; devolve o primeiro se o id não existir.
     *
     * @return array<string, mixed>
     */
    public static function event(int $id): array
    {
        foreach (self::catalogue() as $event) {
            if ($event['id'] === $id) {
                return $event;
            }
        }

        return self::catalogue()[0];
    }

    /**
     * Candidaturas do voluntário.
     *
     * @return list<array{id: int, event: string, company: string, date: string, state: string}>
     */
    public static function applications(): array
    {
        return array_map(
            static fn (array $event): array => [
                'id' => $event['id'],
                'event' => $event['title'],
                'company' => $event['company'],
                'date' => $event['date'],
                'state' => $event['myApplication'],
            ],
            self::events(),
        );
    }

    /**
     * Perfil do voluntário com o histórico de participações.
     *
     * @return array<string, mixed>
     */
    public static function volunteer(): array
    {
        return [
            'name' => 'Ana Silva',
            'email' => 'ana.silva@email.pt',
            'skills' => 'Primeiros socorros, Comunicação',
            'birth' => '12/04/1998',
            'total' => '12,5 h',
            'history' => [
                ['event' => 'Apoio a idosos', 'meta' => '10/10/2026 · Turno da manhã', 'hours' => '2 h'],
                ['event' => 'Recolha solidária', 'meta' => '02/09/2026 · Turno completo', 'hours' => '4,5 h'],
                ['event' => 'Limpeza urbana', 'meta' => '18/07/2026 · Turno da manhã', 'hours' => '6 h'],
            ],
        ];
    }

    /**
     * Notificações do utilizador.
     *
     * @return list<array{icon: string, title: string, text: string, when: string, unread: bool}>
     */
    public static function notifications(): array
    {
        return [
            ['icon' => 'check-circle', 'title' => 'A tua candidatura foi aprovada', 'text' => 'A Associação Horizonte aprovou a tua candidatura.', 'when' => 'Hoje, 10:24', 'unread' => true],
            ['icon' => 'clock', 'title' => 'Lembrete: Limpeza da praia amanhã às 09:00', 'text' => 'Consulta os detalhes do evento antes de participares.', 'when' => 'Ontem', 'unread' => true],
            ['icon' => 'calendar', 'title' => 'Novo evento da Associação Horizonte', 'text' => 'Foi publicado um novo evento que pode interessar-te.', 'when' => '18/11/2026', 'unread' => false],
        ];
    }

    /**
     * Números do dashboard da empresa.
     *
     * @return list<array{label: string, value: string, icon: string}>
     */
    public static function companyStats(): array
    {
        return [
            ['label' => 'Eventos ativos', 'value' => '4', 'icon' => 'calendar'],
            ['label' => 'Candidaturas pendentes', 'value' => '6', 'icon' => 'clipboard'],
            ['label' => 'Voluntários', 'value' => '38', 'icon' => 'users'],
            ['label' => 'Horas geradas', 'value' => '124,5', 'icon' => 'clock'],
            ['label' => 'Taxa de aprovação', 'value' => '78%', 'icon' => 'trending-up'],
        ];
    }

    /**
     * Eventos da empresa (dashboard e "Os meus eventos").
     *
     * @return list<array{id: int, title: string, date: string, state: string, applications: int}>
     */
    public static function companyEvents(): array
    {
        return [
            ['id' => 1, 'title' => 'Limpeza da praia', 'date' => '24/11/2026', 'state' => 'Publicado', 'applications' => 42],
            ['id' => 4, 'title' => 'Apoio a idosos', 'date' => '10/10/2026', 'state' => 'Concluído', 'applications' => 20],
        ];
    }

    /**
     * Candidaturas por decidir nos eventos da empresa.
     *
     * @return list<array{name: string, email: string, event: string, shift: string, date: string, skills: string, state: string}>
     */
    public static function pendingApplications(): array
    {
        return [
            ['name' => 'Ana Silva', 'email' => 'ana.silva@email.pt', 'event' => 'Limpeza da praia', 'shift' => 'Turno da manhã', 'date' => '20/11/2026', 'skills' => 'Primeiros socorros', 'state' => 'Pendente'],
            ['name' => 'João Pereira', 'email' => 'joao.pereira@email.pt', 'event' => 'Limpeza da praia', 'shift' => 'Turno da tarde', 'date' => '20/11/2026', 'skills' => 'Comunicação', 'state' => 'Pendente'],
            ['name' => 'Rita Nunes', 'email' => 'rita.nunes@email.pt', 'event' => 'Limpeza da praia', 'shift' => 'Turno da manhã', 'date' => '21/11/2026', 'skills' => 'Logística', 'state' => 'Pendente'],
        ];
    }

    /**
     * Resumo por turno do relatório do evento.
     *
     * @return list<array{shift: string, seats: int, present: int, hours: string}>
     */
    public static function reportShifts(): array
    {
        return [
            ['shift' => 'Turno da manhã', 'seats' => 24, 'present' => 18, 'hours' => '72 h'],
            ['shift' => 'Turno da tarde', 'seats' => 26, 'present' => 17, 'hours' => '52,5 h'],
        ];
    }

    /**
     * Perfil público da empresa.
     *
     * @return array<string, string>
     */
    public static function company(): array
    {
        return [
            'name' => 'Associação Horizonte',
            'about' => 'A Associação Horizonte promove a inclusão social e o envolvimento da comunidade através de projetos de voluntariado.',
            'website' => 'www.horizonte.pt',
            'location' => 'Leiria',
        ];
    }
}
