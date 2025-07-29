        function limparFormulario() {
            document.getElementById('tipo').value = '';
            document.getElementById('chave').value = '';
            document.getElementById('valor').value = '';
            document.getElementById('nome').value = '';
            document.getElementById('cidade').value = '';
            document.getElementById('descricao').value = '';

            const resultado = document.getElementById('resultado');
            if (resultado) {
                resultado.remove();
            }
            const erro = document.getElementById('erro');
            if (erro) {
                erro.remove();
            }
        }

        function formatarChave(tipo, valor) {
            valor = valor.replace(/\s+/g, '').trim();

            if (tipo === 'cpf') {
                valor = valor.replace(/\D/g, '');
                valor = valor.replace(/^(\d{3})(\d)/, '$1.$2');
                valor = valor.replace(/^(\d{3})\.(\d{3})(\d)/, '$1.$2.$3');
                valor = valor.replace(/\.(\d{3})(\d)/, '.$1-$2');
                if (valor.length > 14) valor = valor.substr(0, 14);

            } else if (tipo === 'cnpj') {
                valor = valor.replace(/\D/g, '');
                valor = valor.replace(/^(\d{2})(\d)/, '$1.$2');
                valor = valor.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
                valor = valor.replace(/\.(\d{3})(\d)/, '.$1/$2');
                valor = valor.replace(/(\d{4})(\d)/, '$1-$2');
                if (valor.length > 18) valor = valor.substr(0, 18);

            } else if (tipo === 'telefone') {
                valor = valor.replace(/\D/g, '');
                if (valor.startsWith('55') === false) {
                    valor = '55' + valor;
                }
                valor = valor.replace(/^55(\d{2})(\d{5})(\d{4}).*/, '+55 $1 $2-$3');
                if (valor.length > 17) valor = valor.substr(0, 17);

            } else if (tipo === 'email') {
                valor = valor.replace(/\s/g, '');
                if (valor.length > 60) valor = valor.substr(0, 60);
            }

            return valor;
        }

        function aplicarMascara() {
            const tipo = document.getElementById('tipo').value;
            let valor = document.getElementById('chave').value;

            if (tipo === '') return;

            valor = formatarChave(tipo, valor);
            document.getElementById('chave').value = valor;
        }

        window.onload = function() {
            document.getElementById('tipo').addEventListener('change', function() {
                document.getElementById('chave').value = '';
            });
            document.getElementById('chave').addEventListener('input', aplicarMascara);
        }

        const form = document.querySelector("form");
        const overlay = document.getElementById("loading-overlay");

        form?.addEventListener("submit", function() {
            overlay.style.display = "flex";
        });

        function limparFormulario() {
            document.querySelector("form").reset();
        }