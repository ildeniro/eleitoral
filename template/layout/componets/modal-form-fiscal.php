<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="form_indicacao" name="form_indicacao" action="#" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Cadastro de Referência de Indicação</h5>
                    <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </a>
                </div>
                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-12">
                            <div id="div_modal_nome" class="form-group">
                                <label for="modal_nome">Nome <b class="error">*</b></label></label>
                                <input type="text" class="form-control" name="modal_nome" id="modal_nome" value=""/>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div id="div_modal_funcao" class="form-group">
                                <label for="modal_funcao">Função <b class="error">*</b></label></label>
                                <input type="text" class="form-control" name="modal_funcao" id="modal_funcao" value=""/>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div id="div_modal_telefone" class="form-group">
                                <label for="modal_telefone">Telefone <b class="error">*</b></label>
                                <input type="text" class="form-control" data-mask="(99) 9 9999-9999" name="modal_telefone" id="modal_telefone" value=""/>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <a href="#" id="salvar_modal" class="btn btn-primary">Salvar</a>
                    <a href="#" class="btn btn-secondary" data-dismiss="modal">Fechar</a>
                </div>
            </form>
        </div>
    </div>
</div>