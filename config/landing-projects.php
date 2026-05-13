<?php

declare(strict_types=1);

/**
 * Projetos estáticos exibidos na landing page do LabSIS.
 *
 * Para adicionar um novo projeto, basta copiar o bloco abaixo e preencher:
 *
 *   [
 *       'name'        => 'Nome do Projeto',
 *       'logo'        => 'images/projetos/logo-do-projeto.png',  // caminho relativo a public/
 *       'category'    => 'SaaS',                                  // Landing Page | SaaS | Mobile | Bot
 *       'description' => 'Descrição curta do projeto (máx ~80 caracteres).',
 *       'url'         => 'https://url-do-projeto.com.br',
 *   ],
 */

return [

    [
        'name' => 'GETEC 2026',
        'logo' => 'images/projetos/getec2026.jpeg',
        'category' => 'Landing Page',
        'description' => 'Site do evento GETEC 2026 do curso de Gestão da Produção Industrial no Campus Araguaina.',
        'url' => 'https://gpiaraguaina.github.io/getec/',
    ],

    [
        'name' => 'Safe+ - Plataforma de Avaliação Física Escolar Inclusiva',
        'logo' => 'images/projetos/safe.webp',
        'category' => 'SaaS',
        'description' => 'SaaS para avaliação física escolar inclusiva. Automatiza protocolos do PROESP-Br e fornece dados de saúde para inteligência governamental.',
        'url' => 'https://safe-oficial-main-dnfz5k.laravel.cloud/',
    ],

];
