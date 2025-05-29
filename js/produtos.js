// Função para pegar os produtos do localStorage ou iniciar vazio
function getProdutos() {
    const produtosJSON = localStorage.getItem('produtos');
    return produtosJSON ? JSON.parse(produtosJSON) : [];
}

// Função para salvar produtos no localStorage
function salvarProdutos(produtos) {
    localStorage.setItem('produtos', JSON.stringify(produtos));
}

// Função para renderizar os produtos cadastrados
function renderizarProdutos() {
    const lista = document.getElementById('listaProdutos');
    lista.innerHTML = '';

    const produtos = getProdutos();

    if (produtos.length === 0) {
        lista.innerHTML = '<p class="text-muted">Nenhum produto cadastrado.</p>';
        return;
    }

    produtos.forEach(produto => {
        const col = document.createElement('div');
        col.classList.add('col-6', 'col-md-3');

        col.innerHTML = `
      <div class="card h-100">
        <img src="${produto.img}" class="card-img-top produto-img" alt="${produto.nome}">
        <div class="card-body">
          <h6 class="card-title">${produto.nome}</h6>
          <p class="card-text">R$ ${produto.preco.toFixed(2)}</p>
          <button class="btn btn-danger btn-sm btn-remover" data-id="${produto.id}">Remover</button>
        </div>
      </div>
    `;

        lista.appendChild(col);
    });
}

// Gerar ID único simples
function gerarId() {
    return Date.now() + Math.floor(Math.random() * 1000); // para evitar duplicados rápidos
}

// Manipulação do formulário — **MUDANÇA AQUI** para ler a imagem como base64 via FileReader
document.getElementById('formProduto').addEventListener('submit', e => {
    e.preventDefault();

    const nome = document.getElementById('nomeProduto').value.trim();
    const preco = parseFloat(document.getElementById('precoProduto').value);
    const inputImagem = document.getElementById('imagemProduto');

    if (!nome || isNaN(preco) || preco < 0 || inputImagem.files.length === 0) {
        alert('Preencha todos os campos corretamente.');
        return;
    }

    const file = inputImagem.files[0];
    const reader = new FileReader();

    reader.onload = function (event) {
        const imgBase64 = event.target.result;

        const produtos = getProdutos();
        produtos.push({id: gerarId(), nome, preco, img: imgBase64});
        salvarProdutos(produtos);
        renderizarProdutos();

        e.target.reset();
    };

    reader.readAsDataURL(file);
});

// Remover produto ao clicar no botão
document.getElementById('listaProdutos').addEventListener('click', e => {
    if (e.target.classList.contains('btn-remover')) {
        const id = e.target.dataset.id;
        let produtos = getProdutos();
        produtos = produtos.filter(prod => prod.id != id);
        salvarProdutos(produtos);
        renderizarProdutos();
    }
});

// Inicializa a lista na carga da página
renderizarProdutos();


//mascara valor
document.getElementById('precoProduto').addEventListener('input', function (e) {
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