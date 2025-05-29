const formCadastroCliente = document.getElementById('formCadastroCliente');
const listaClientes = document.getElementById('listaClientes');

let clientes = JSON.parse(localStorage.getItem('clientes')) || [];

function salvarClientes() {
    localStorage.setItem('clientes', JSON.stringify(clientes));
}

function atualizarListaClientes() {
    listaClientes.innerHTML = '';
    if (clientes.length === 0) {
        listaClientes.innerHTML = '<li class="list-group-item">Nenhum cliente cadastrado.</li>';
        return;
    }
    clientes.forEach((cliente, index) => {
        const li = document.createElement('li');
        li.classList.add('list-group-item', 'd-flex', 'justify-content-between', 'align-items-center');
        li.textContent = `${cliente.nome} - Tel: ${cliente.telefone} - Endereço: ${cliente.endereco}`;

        // Botão de remover
        const btnRemover = document.createElement('button');
        btnRemover.classList.add('btn', 'btn-danger', 'btn-sm');
        btnRemover.textContent = 'X';
        btnRemover.addEventListener('click', () => {
            if (confirm(`Deseja realmente remover o cliente "${cliente.nome}"?`)) {
                clientes.splice(index, 1);
                salvarClientes();
                atualizarListaClientes();
            }
        });

        li.appendChild(btnRemover);
        listaClientes.appendChild(li);
    });
}

formCadastroCliente.addEventListener('submit', (e) => {
    e.preventDefault();

    const nome = document.getElementById('nomeCliente').value.trim();
    const telefone = document.getElementById('telefoneCliente').value.trim();
    const endereco = document.getElementById('enderecoCliente').value.trim();

    if (!nome || !telefone || !endereco) {
        alert('Por favor, preencha todos os campos.');
        return;
    }

    clientes.push({nome, telefone, endereco});
    salvarClientes();
    atualizarListaClientes();

    formCadastroCliente.reset();
});

atualizarListaClientes();

//mascara telefone
document.getElementById('telefoneCliente').addEventListener('input', function (e) {
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
