#!/bin/bash

# Verifica se foi fornecido algum argumento
if [ $# -eq 0 ]; then
    echo "Uso: ./commit.sh <mensagem do commit>"
    exit 1
fi

# Executa o Laravel Pint para formatação do código
echo "Executando Laravel Pint para formatação do código..."
./vendor/bin/pint --parallel
if [ $? -ne 0 ]; then
    echo "Erro ao executar Laravel Pint"
    exit 1
fi

# Adiciona todas as mudanças ao staging area
echo "Adicionando alterações ao staging area..."
git add .

# Pega todos os argumentos passados e junta em uma única string
commit_message="$*"

# Realiza o commit com o comentário fornecido
# Realiza o commit com o comentário fornecido
echo "Realizando commit: $commit_message"
git commit -m "$commit_message"

# Realiza o push para o repositório remoto
echo "Realizando git push..."
git push
