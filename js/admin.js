// Validar o campo select do formulário
function validateForm() {
    // Pega o elemento pelo id que chama itemorder
    let itemOrder = document.getElementById("itemorder").value;

    // Se o valor da option for "0"
    if (itemOrder === "0") {
        alert("Por favor, selecione um Item de Pedido válido!");
        // Impede o envio do formulário se não tiver selecionado nada no campo select
        return false;
    }
    return true; // Permite o envio do formulário
}

// Adiciona um evento de escuta. Quando o campo pegado pelo id for manipulado ele vai agir.
// É acionado sempre que o conteúdo do campo é alterado 
document.getElementById("phone").addEventListener("input", function(event) { //Função anônima passada como segundo parâmetro é executada a cada interação com o campo.  
    
    let phone = event.target.value; // Captura o valor atual do camp. Target é o elemento no qual o evento ocorreu.

    // Remove qualquer caractere não numérico que a pessoa tentar digitar
    phone = phone.replace(/\D/g, '');

    // Se o campo estiver vazio, não aplica nenhuma formatação
    if (phone.length === 0) {
        event.target.value = '';
        return;
    }

    // Aplica a formatação para o telefone
    if (phone.length <= 2) {
        // Se os tamanho do campo for menor ou igual a 2 adiciona um parentese ao lado do phone (que é o campo sendo manipulado)
        phone = `(${phone}`;
    } else if (phone.length <= 7) { 
        // Se tem entre 3 e 7 números, adiciona o parêntese e o espaço após o DDD
        phone = `(${phone.slice(0, 2)}) ${phone.slice(2)}`; //slice divide a string phone em partes e aplica a formatação adequada
    } else {
        // Se tem mais de 7 números, adiciona o parêntese, espaço e o hífen
        phone = `(${phone.slice(0, 2)}) ${phone.slice(2, 7)}-${phone.slice(7, 11)}`;
    }

    // Atribui o valor formatado "phone" de volta ao campo de entrada "event.target.value".
    event.target.value = phone;
});