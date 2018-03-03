<?php
  /*
    Autor: Magno Oliveira
    Data: 01/03/2018
    Nome do projeto: Agenda de Contatos (Avaliação Prática)
  */
  session_start();
  $modo = "";
  //Estabelece conexão com Banco
  $conn = mysqli_connect("localhost", "root", "bcd127", "db_agenda");
  //Verifica se ocorreu algum erro na conexão
  if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
  }
  //Verifica se o botão de submit foi clicado
  if(isset($_POST['txtNome'])){

    $nome = $_POST['txtNome'];
    $celular = $_POST['txtCelular'];
    $email = $_POST['txtEmail'];
    //Verifica se a ação é para atualizar dados ou inserir novos dados
    if($_POST['btnSalvar'] == "Editar"){

      $id = $_SESSION['id'];
      $editar = "UPDATE contato
                  SET nome = '$nome',
                      email = '$email',
                      celular = '$celular'
                  WHERE id = '$id'";
      $_SESSION = array();
      mysqli_query($conn, $editar);

    }else{

      $inserir = "INSERT INTO contato
                  SET nome = '$nome',
                      email = '$email',
                      celular = '$celular';";
      mysqli_query($conn, $inserir);

    }

  }
  //Verifica se a ação é para excluir ou procurar por dados pelo id
  if(isset($_GET['modo'])){

    $modo = $_GET['modo'];
    $id = $_GET['id'];
    $_SESSION['id'] = $id;
    $idContato = $id;
    switch($modo){
      case 'excluir':

        $excluir = "DELETE FROM contato WHERE id = $id";

        mysqli_query($conn, $excluir);

        break;
      case 'editar':

        $pesquisar = "SELECT * FROM contato WHERE id = $id";

        $dadosContato = mysqli_query($conn, $pesquisar);

        if($contato = mysqli_fetch_array($dadosContato)){

          $nome = $contato['nome'];
          $celular = $contato['celular'];
          $email = $contato['email'];

        } else{
          echo("<script>
                  alert(Ocorreu um erro durante o carregamento dos dados do contato..);
                <script>");
        }

        break;

    }
  }

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Agenda de Contatos</title>
    <script type="text/javascript" src="js/jquery-3.2.1.slim.min.js"></script>
    <script type="text/javascript" src="js/pooper.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/style.css">
    <script type="text/javascript">
      /* Máscaras Para Telefone e Celular */
      function mascara(o,f){
          v_obj=o
          v_fun=f
          setTimeout("execmascara()",1)//Inicia a função execmascara
      }
      function execmascara(){
          v_obj.value=v_fun(v_obj.value)
      }
      function mascaraCelular(v){   //Mascara para o celular
          v=v.replace(/\D/g,"");             //Remove tudo o que não é dígito
          v=v.replace(/^(\d{2})(\d)/g,"($1) $2"); //Coloca parênteses em volta dos dois primeiros dígitos
          v=v.replace(/(\d)(\d{4})$/,"$1-$2");    //Coloca hífen entre o quarto e o quinto dígitos
          return v;
      }
      function id( el ){ //Função para pegar os elementos da caixa
        return document.getElementById( el );
      }
      window.onload = function(){

        id('celular').onkeypress = function(){
          mascara( this, mascaraCelular)
        }
      }

    </script>
  </head>
  <body>
    
    <section class="container">
      <div class="text-center my-5">
        <div class="my-5">
          <h2 class="display-4 text-primary">Agenda de Contatos</h2>
          <span class="h6 d-block">Desenvolvido por Magno Oliveira.</span>
        </div>
      </div>

      <!-- Formulário -->

      <div class="row justify-content-center my-3">
        <form name="frmcontatos" action="contatos.php" method="post">
          <div class="form-row justify-content-center">
            <div class="form-group col-sm-9">
              <label for="nomeContato">Nome do Contato</label>
              <input type="text" name="txtNome" required class="form-control" value="<?php echo( $modo=='editar' ? $nome : ""); ?>">
            </div>
            <div class="form-group col-sm-3">
              <label for="celularContato">Celular</label>
              <input id="celular" type="tel" required class="form-control text-center" id="CelularContato" name="txtCelular" placeholder="xx xxxxx-xxxx" maxlength="15" value="<?php echo( $modo=='editar' ? $celular : ""); ?>">
            </div>
            <div class="form-group col-md-12">
              <label for="nomeEmail">Email</label>
              <input type="email" class="form-control" required name="txtEmail" id="nomeEmail" aria-describedby="emailHelp" value="<?php echo( $modo=='editar' ? $email : ""); ?>">
            </div>
            <div class="form-group">
              <input type="submit" class="btn btn-primary p-2"  name="btnSalvar" value="<?php echo( $modo=='editar' ? "Editar" : "Salvar"); ?>"/>
            </div>
          </div>
        </form>

      </div>

      <!-- Fim do Formulário -->

      <!-- Tabela de Contatos -->
      <table class="table table-hover table-responsive-md">
        <thead>
          <tr>
            <th scope="col">Id</th>
            <th scope="col">Nome</th>
            <th scope="col">Email</th>
            <th scope="col">Celular</th>
            <th scope="col">Editar/Remover</th>
          </tr>
        </thead>
        <tbody>
        <!-- Início do carregamento de dados -->
          <?php

          $sql = "SELECT * FROM contato;";

          $select = mysqli_query($conn, $sql);

          if(mysqli_num_rows($select) > 0){

            while($rs = mysqli_fetch_array($select)){

              $id = $rs['id'];

           ?>
          <tr>
            <th scope="row"><?php echo($id); ?></th>
            <td><?php echo($rs['nome']); ?></td>
            <td><?php echo($rs['email']); ?></td>
            <td><?php echo($rs['celular']); ?></td>
            <td>
              <!--  -->
              <a href="contatos.php?modo=editar&id=<?php echo($rs['id']); ?>" class="mx-2">
                <img src="icones/editar.png" alt="Editar">
              </a>
              <!--  -->
              <a href="contatos.php?modo=excluir&id=<?php echo($rs['id']); ?>" class="mx-2" onclick="return confirm('Deseja realmente excluir este contato?');">
                <img src="icones/excluir.png" alt="Excluir">
              </a>
            </td>
          </tr>
          <?php
            }
          }
          // Fim do Carregamento de Dados
          ?>
        </tbody>
      </table>
      <!-- Fim da tabela -->
    </section>
  </body>
</html>
