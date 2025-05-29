//mascara telefone
document.getElementById('telefone').addEventListener('input', function (e) {
    let value = e.target.value.replace(/\D/g, ''); // Remove tudo que não é dígito

    if (value.length > 11)
        value = value.slice(0, 11); // Limita a 11 dígitos

    // Aplica a máscara
    if (value.length >= 2) {
        value = '(' + value.slice(0, 2) + ') ' + value.slice(2);
    }
    if (value.length >= 9) {
        value = value.slice(0, 10) + '-' + value.slice(10);
    }

    e.target.value = value;
});

//mascara valor
document.getElementById('valor').addEventListener('input', function (e) {
    let value = e.target.value;

    // Remove tudo que não é número
    value = value.replace(/\D/g, '');

    // Converte para decimal (centavos)
    value = (parseInt(value, 10) / 100).toFixed(2);

    // Formata para o formato brasileiro
    value = value
        .replace('.', ',')                       // troca ponto por vírgula
        .replace(/\B(?=(\d{3})+(?!\d))/g, '.');  // adiciona ponto para milhar

    e.target.value = 'R$ ' + value;
});

