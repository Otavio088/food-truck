// Seleciona o botão do menu hamburger
const btnMobile = document.getElementById('btn-mobile');

// Função para alternar a classe 'active' no menu
function menuEffect() {
    // Atribui o elemento nav pelo id que é nav
    const nav = document.getElementById('nav'); 
    // Seleciona lista de classes do css com classList
    nav.classList.toggle('active'); // toggle adiciona a classe caso nao exista e remove caso exista
}

// Adiciona um listener de clique ao botão hamburger
btnMobile.addEventListener('click', menuEffect); // Adicionado este trecho
