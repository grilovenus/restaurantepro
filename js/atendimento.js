// Dados simulados de clientes
const clientes = JSON.parse(localStorage.getItem('clientes')) || [
    {id: 1, nome: 'Cliente Final'},
    {id: 2, nome: 'João Silva'},
    {id: 3, nome: 'Maria Oliveira'}
];

const produtos = JSON.parse(localStorage.getItem('produtos')) || [
    {id: 1, nome: 'Pizza', preco: 30.0, img: 'https://via.placeholder.com/150'},
    {id: 2, nome: 'Refrigerante', preco: 5.5, img: 'https://via.placeholder.com/150'},
    {id: 3, nome: 'Suco', preco: 7.0, img: 'https://via.placeholder.com/150'},
];

const inputCliente = document.getElementById('nomeCliente');
const sugestoesDiv = document.getElementById('sugestoesClientes');

inputCliente.addEventListener('input', () => {
    const termo = inputCliente.value.trim().toLowerCase();
    sugestoesDiv.innerHTML = '';

    if (termo === '') {
        sugestoesDiv.style.display = 'none';
        return;
    }

    const encontrados = clientes.filter(c => c.nome.toLowerCase().includes(termo));
    if (encontrados.length === 0) {
        sugestoesDiv.style.display = 'none';
        return;
    }

    encontrados.forEach(cliente => {
        const item = document.createElement('button');
        item.type = 'button';
        item.className = 'list-group-item list-group-item-action';
        item.textContent = cliente.nome;
        item.addEventListener('click', () => {
            inputCliente.value = cliente.nome;
            sugestoesDiv.innerHTML = '';
            sugestoesDiv.style.display = 'none';
        });
        sugestoesDiv.appendChild(item);
    });

    sugestoesDiv.style.display = 'block';
});

document.addEventListener('click', (event) => {
    if (!sugestoesDiv.contains(event.target) && event.target !== inputCliente) {
        sugestoesDiv.innerHTML = '';
        sugestoesDiv.style.display = 'none';
    }
});

const produtosContainer = document.getElementById('produtosContainer').querySelector('.row');
const tabelaItens = document.getElementById('tabelaItens');
const totalGeralSpan = document.getElementById('totalGeral');

produtosContainer.innerHTML = '';

produtos.forEach(produto => {
    const col = document.createElement('div');
    col.classList.add('col-6', 'col-md-4');
    col.innerHTML = `
    <div class="card produto-card" data-id="${produto.id}">
      <img src="${produto.img}" class="card-img-top produto-img" alt="${produto.nome}">
      <div class="card-body text-center">
        <h6 class="card-title">${produto.nome}</h6>
        <p class="card-text">R$ ${produto.preco.toFixed(2)}</p>
      </div>
    </div>`;
    produtosContainer.appendChild(col);
});

function atualizarTotais() {
    let totalGeral = 0;
    tabelaItens.querySelectorAll('tr').forEach(row => {
        const preco = parseFloat(row.dataset.preco);
        const qtd = parseInt(row.querySelector('.qtd').value);
        const total = preco * qtd;
        row.querySelector('.total').textContent = total.toFixed(2);
        totalGeral += total;
    });
    totalGeralSpan.textContent = totalGeral.toFixed(2);
    atualizarBotoesEValores();
}

function adicionarProduto(id) {
    const produto = produtos.find(p => p.id == id);
    if (!produto)
        return;

    const existente = tabelaItens.querySelector(`tr[data-id="${id}"]`);
    if (existente) {
        const qtdInput = existente.querySelector('.qtd');
        qtdInput.value = parseInt(qtdInput.value) + 1;
        atualizarTotais();
        return;
    }

    const tr = document.createElement('tr');
    tr.setAttribute('data-id', id);
    tr.setAttribute('data-preco', produto.preco);
    tr.innerHTML = `
    <td>${produto.nome}</td>
    <td><input type="number" class="form-control qtd" value="1" min="1" style="width: 70px;"></td>
    <td class="total">${produto.preco.toFixed(2)}</td>
    <td><button class="btn btn-danger btn-sm remover">X</button></td>`;
    tabelaItens.appendChild(tr);
    atualizarTotais();
}

produtosContainer.addEventListener('click', e => {
    const card = e.target.closest('.produto-card');
    if (card)
        adicionarProduto(card.dataset.id);
});

tabelaItens.addEventListener('input', e => {
    if (e.target.classList.contains('qtd')) {
        if (e.target.value < 1)
            e.target.value = 1;
        atualizarTotais();
    }
});

tabelaItens.addEventListener('click', e => {
    if (e.target.classList.contains('remover')) {
        e.target.closest('tr').remove();
        atualizarTotais();
    }
});

const finalizarBtn = document.getElementById('finalizarAtendimento');
const modalPagamento = new bootstrap.Modal(document.getElementById('modalPagamento'));
const formPagamento = document.getElementById('formPagamento');
const pagamentosContainer = document.getElementById('pagamentosContainer');
const btnAdicionarPagamento = document.getElementById('adicionarPagamento');
const totalGeralModalSpan = document.getElementById('totalGeralModal');
const totalInformadoSpan = document.getElementById('totalInformado');
const avisoErro = document.getElementById('avisoErro');
const confirmarPagamentoBtn = document.getElementById('confirmarPagamentoBtn');

function criarLinhaPagamento() {
    const div = document.createElement('div');
    div.classList.add('mb-3', 'd-flex', 'align-items-center', 'gap-2');
    div.innerHTML = `
    <select class="form-select forma-pagamento" required style="flex:1;">
      <option value="" disabled selected>Selecione forma</option>
      <option value="dinheiro">Dinheiro</option>
      <option value="cartao_credito">Cartão de Crédito</option>
      <option value="cartao_debito">Cartão de Débito</option>
      <option value="pix">Pix</option>
      <option value="bonificacao">Bonificação</option>
      <option value="a_prazo">A Prazo</option>
    </select>
    <input type="number" class="form-control valor-pagamento" placeholder="Valor (R$)" min="0.01" step="0.01" required style="width:120px;" />
    <input type="number" class="form-control input-parcelas d-none" min="2" max="36" placeholder="Parcelas" style="width:100px;" />
    <button type="button" class="btn btn-danger btn-sm btn-remover-pagamento">X</button>`;
    return div;
}

btnAdicionarPagamento.addEventListener('click', () => {
    const linha = criarLinhaPagamento();
    pagamentosContainer.appendChild(linha);
    atualizarBotoesEValores();
    linha.querySelector('.forma-pagamento').focus();
});

pagamentosContainer.addEventListener('click', e => {
    if (e.target.classList.contains('btn-remover-pagamento')) {
        e.target.parentElement.remove();
        atualizarBotoesEValores();
    }
});

pagamentosContainer.addEventListener('input', e => {
    if (e.target.classList.contains('forma-pagamento')) {
        const div = e.target.closest('div.mb-3');
        const inputParcelas = div.querySelector('.input-parcelas');
        if (e.target.value === 'a_prazo') {
            inputParcelas.classList.remove('d-none');
            inputParcelas.setAttribute('required', 'required');
        } else {
            inputParcelas.classList.add('d-none');
            inputParcelas.removeAttribute('required');
            inputParcelas.value = '';
        }
    }
    atualizarBotoesEValores();
});

function atualizarBotoesEValores() {
    const totalGeral = parseFloat(totalGeralSpan.textContent.replace(',', '.')) || 0;
    totalGeralModalSpan.textContent = totalGeral.toFixed(2);

    let totalInformado = 0;
    let tudoValido = true;
    let dinheiroInformado = 0;

    pagamentosContainer.querySelectorAll('div.mb-3').forEach(div => {
        const valor = parseFloat(div.querySelector('.valor-pagamento').value) || 0;
        const forma = div.querySelector('.forma-pagamento').value;
        const parcelas = parseInt(div.querySelector('.input-parcelas')?.value);

        if (!forma || valor <= 0 || (forma === 'a_prazo' && (!parcelas || parcelas < 2 || parcelas > 36))) {
            tudoValido = false;
        } else {
            totalInformado += valor;
            if (forma === 'dinheiro')
                dinheiroInformado += valor;
        }
    });

    totalInformadoSpan.textContent = totalInformado.toFixed(2);
    const troco = dinheiroInformado > totalGeral ? dinheiroInformado - totalGeral : 0;
    document.getElementById('trocoValor').textContent = troco.toFixed(2);

    confirmarPagamentoBtn.disabled = !(tudoValido && totalInformado >= totalGeral);
    avisoErro.style.display = confirmarPagamentoBtn.disabled ? 'block' : 'none';
}

finalizarBtn.addEventListener('click', () => {
    let nomeCliente = document.getElementById('nomeCliente').value.trim();
    if (!nomeCliente) {
        nomeCliente = 'Cliente Final';
    } else {
        const existe = clientes.some(c => c.nome.toLowerCase() === nomeCliente.toLowerCase());
        if (!existe)
            nomeCliente = 'Cliente Final';
    }

    if (tabelaItens.children.length === 0)
        return alert('Adicione ao menos um produto ao atendimento.');
    modalPagamento.show();
});

formPagamento.addEventListener('submit', e => {
    e.preventDefault();

    let nomeCliente = document.getElementById('nomeCliente').value.trim();
    if (!nomeCliente) {
        nomeCliente = 'Cliente Final';
    } else {
        const existe = clientes.some(c => c.nome.toLowerCase() === nomeCliente.toLowerCase());
        if (!existe)
            nomeCliente = 'Cliente Final';
    }

    const pagamentos = [];
    let valido = true;

    pagamentosContainer.querySelectorAll('div.mb-3').forEach(div => {
        const forma = div.querySelector('.forma-pagamento').value;
        const valor = parseFloat(div.querySelector('.valor-pagamento').value) || 0;
        const parcelas = parseInt(div.querySelector('.input-parcelas')?.value);

        if (!forma || valor <= 0 || (forma === 'a_prazo' && (!parcelas || parcelas < 2 || parcelas > 36))) {
            valido = false;
        } else {
            pagamentos.push({forma, valor, parcelas: forma === 'a_prazo' ? parcelas : null});
        }
    });

    if (!valido || pagamentos.length === 0) {
        alert('Verifique as formas de pagamento.');
        return;
    }

    const somaPagamentos = pagamentos.reduce((acc, cur) => acc + cur.valor, 0);
    const totalGeral = parseFloat(totalGeralSpan.textContent) || 0;

    if (Math.abs(somaPagamentos - totalGeral) > 0.01) {
        alert('O valor informado nos pagamentos não confere com o total da compra.');
        return;
    }

    const vendas = JSON.parse(localStorage.getItem('vendas')) || [];
    vendas.push({
        id: Date.now(),
        cliente: nomeCliente,
        pagamentos,
        total: totalGeral,
        data: new Date().toISOString()
    });
    localStorage.setItem('vendas', JSON.stringify(vendas));

    alert(`Atendimento finalizado para ${nomeCliente}.
Pagamentos:\n${pagamentos.map(p => {
        let linha = `${p.forma}: R$ ${p.valor.toFixed(2)}`;
        if (p.parcelas)
            linha += ` em ${p.parcelas}x de R$ ${(p.valor / p.parcelas).toFixed(2)}`;
        return linha;
    }).join('\n')}`);

    formPagamento.reset();
    pagamentosContainer.innerHTML = '';
    confirmarPagamentoBtn.disabled = true;
    tabelaItens.innerHTML = '';
    totalGeralSpan.textContent = '0.00';
    document.getElementById('nomeCliente').value = '';
    modalPagamento.hide();
});

