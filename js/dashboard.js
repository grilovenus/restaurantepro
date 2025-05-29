// Variáveis de referência dos elementos
const tabelaVendas = document.querySelector('#tabelaVendas tbody');
const filtroDataInicio = document.getElementById('filtroDataInicio');
const filtroDataFim = document.getElementById('filtroDataFim');
const filtroFormaPagamento = document.getElementById('filtroFormaPagamento');
const filtroCliente = document.getElementById('filtroCliente');
const nenhumaVenda = document.getElementById('nenhumaVenda');
const totalFiltradoSpan = document.getElementById('totalFiltrado');
const totalGeralSpan = document.getElementById('totalGeral');

let vendas = [];

// Carrega vendas do localStorage e aplica filtros
function carregarVendas() {
    vendas = JSON.parse(localStorage.getItem('vendas')) || [];
    aplicarFiltros();
}

// Aplica os filtros e atualiza a tabela e os totais
function aplicarFiltros() {
    const inicio = filtroDataInicio.value ? new Date(filtroDataInicio.value + 'T00:00:00') : null;
    const fim = filtroDataFim.value ? new Date(filtroDataFim.value + 'T23:59:59') : null;
    const forma = filtroFormaPagamento.value;
    const cliente = filtroCliente.value.toLowerCase();

    tabelaVendas.innerHTML = '';
    let totalFiltrado = 0;
    let totalGeral = 0;

    vendas.forEach(venda => {
        const dataVenda = new Date(venda.data);
        totalGeral += venda.total;

        const atendeFiltro =
                (!inicio || dataVenda >= inicio) &&
                (!fim || dataVenda <= fim) &&
                (!forma || venda.pagamentos.some(p => p.forma === forma)) &&
                (!cliente || venda.cliente.toLowerCase().includes(cliente));

        if (atendeFiltro) {
            totalFiltrado += venda.total;

            const dataFormatada = dataVenda.toLocaleString('pt-BR', {
                day: '2-digit', month: '2-digit', year: 'numeric',
                hour: '2-digit', minute: '2-digit'
            });

            const pagamentosDescricao = venda.pagamentos.map(p => {
                let desc = `${p.forma.replace('_', ' ')}: R$ ${p.valor.toFixed(2)}`;
                if (p.parcelas) {
                    desc += ` (${p.parcelas}x de R$ ${(p.valor / p.parcelas).toFixed(2)})`;
                }
                return desc;
            }).join('<br>');

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${venda.cliente}</td>
                <td>${dataFormatada}</td>
                <td>R$ ${venda.total.toFixed(2)}</td>
                <td>${pagamentosDescricao}</td>
                <td><button class="btn btn-danger btn-sm" onclick="excluirVenda(${venda.id})">Excluir</button></td>
            `;
            tabelaVendas.appendChild(tr);
        }
    });

    nenhumaVenda.style.display = tabelaVendas.children.length === 0 ? 'block' : 'none';
    totalFiltradoSpan.textContent = totalFiltrado.toFixed(2);
    totalGeralSpan.textContent = totalGeral.toFixed(2);
}

// Excluir venda pelo ID
function excluirVenda(id) {
    if (confirm('Deseja realmente excluir esta venda?')) {
        vendas = vendas.filter(v => v.id !== id);
        localStorage.setItem('vendas', JSON.stringify(vendas));
        aplicarFiltros();
    }
}

// Eventos para filtros — atualiza ao alterar
[filtroDataInicio, filtroDataFim, filtroFormaPagamento, filtroCliente].forEach(input => {
    input.addEventListener('input', aplicarFiltros);
});

// Carrega vendas ao abrir a página
window.addEventListener('DOMContentLoaded', carregarVendas);




function aplicarFiltros() {
    const inicio = filtroDataInicio.value ? new Date(filtroDataInicio.value + 'T00:00:00') : null;
    const fim = filtroDataFim.value ? new Date(filtroDataFim.value + 'T23:59:59') : null;
    const forma = filtroFormaPagamento.value;
    const cliente = filtroCliente.value.toLowerCase();

    tabelaVendas.innerHTML = '';
    let totalFiltrado = 0;
    let totalGeral = 0;

    // Ordena as vendas do mais recente para o mais antigo
    const vendasOrdenadas = vendas.slice().sort((a, b) => new Date(b.data) - new Date(a.data));

    vendasOrdenadas.forEach(venda => {
        const dataVenda = new Date(venda.data);
        totalGeral += venda.total;

        const atendeFiltro =
                (!inicio || dataVenda >= inicio) &&
                (!fim || dataVenda <= fim) &&
                (!forma || venda.pagamentos.some(p => p.forma === forma)) &&
                (!cliente || venda.cliente.toLowerCase().includes(cliente));

        if (atendeFiltro) {
            totalFiltrado += venda.total;

            const dataFormatada = dataVenda.toLocaleString('pt-BR', {
                day: '2-digit', month: '2-digit', year: 'numeric',
                hour: '2-digit', minute: '2-digit'
            });

            const pagamentosDescricao = venda.pagamentos.map(p => {
                let desc = `${p.forma.replace('_', ' ')}: R$ ${p.valor.toFixed(2)}`;
                if (p.parcelas) {
                    desc += ` (${p.parcelas}x de R$ ${(p.valor / p.parcelas).toFixed(2)})`;
                }
                return desc;
            }).join('<br>');

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${venda.cliente}</td>
                <td>${dataFormatada}</td>
                <td>R$ ${venda.total.toFixed(2)}</td>
                <td>${pagamentosDescricao}</td>
                <td><button class="btn btn-danger btn-sm" onclick="excluirVenda(${venda.id})">Excluir</button></td>
            `;
            tabelaVendas.appendChild(tr);
        }
    });

    nenhumaVenda.style.display = tabelaVendas.children.length === 0 ? 'block' : 'none';
    totalFiltradoSpan.textContent = totalFiltrado.toFixed(2);
    totalGeralSpan.textContent = totalGeral.toFixed(2);
}
