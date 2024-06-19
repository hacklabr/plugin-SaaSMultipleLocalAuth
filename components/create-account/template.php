<?php
/**
 * @var \MapasCulturais\Themes\BaseV2\Theme $this
 * @var \MapasCulturais\App $app
 * 
 */

use MapasCulturais\i;

$this->import('
    entity-field
    entity-terms
    mc-card
    mc-icon
    mc-stepper
    password-strongness
');
?>

<div class="create-account"> 

    <div class="create-account__title">
        <label><?= $this->text('title', i::__('Novo cadastro')) ?> </label>
        <p v-if="totalSteps == 1"><?= sprintf($this->text('description', i::__('Preencha os campos abaixo para criar seu cadastro no %s.')), $app->siteName) ?> </p>
        <p v-if="totalSteps > 1"><?= sprintf($this->text('step-description', i::__('Siga os passos para criar o seu cadastro no %s.')), $app->siteName) ?> </p>
    </div>

    <!-- Creating account -->
    <mc-card v-if="!created && !creating" class="no-title">        
        <template #content> 
            <div v-if="totalSteps >= 1" class="create-account__timeline">
                <mc-stepper :steps="arraySteps" disable-navigation no-labels></mc-stepper>
            </div>

            <!-- First step -->
            <div v-if="step==0" class="create-account__step grid-12">
                <form class="col-12 grid-12" @submit.prevent="nextStep();">
                    <entity-field :entity="agent" classes="col-12" hide-required label=<?php i::esc_attr_e("Nome")?> prop="name" fieldDescription="<?= i::__('As pessoas irão encontrar você por esse nome.') ?>"></entity-field>

                    <div class="field col-12">
                        <label for="email"> <?= i::__('E-mail') ?> </label>
                        <input type="text" name="email" id="email" v-model="email" />
                    </div>

                    <div class="field col-12">
                        <label class="create-account__cpf-label" for="cpf"> 
                            <?= i::__('CPF') ?> 
                            <div class="question">
                                <VMenu show>
                                    <button tabindex="-1" class="create-account__cpf-tip-button" type="button"> <?= i::__('Por que pedimos este dado') ?> <mc-icon name="question"></mc-icon> </button>
                                    <template #popper>
                                        <small class="create-account__cpf-tip">
                                            <?= $this->text('why-cpf', i::__('O CPF é a informação chave para evitarmos a criação de usuários duplicados no sistema.')) ?>
                                        </small>
                                    </template>
                                </VMenu>
                            </div>
                        </label>
                        <input type="text" name="cpf" id="cpf" v-model="cpf" @change="cpfMask" maxlength="14" />
                    </div>

                    <div class="field col-12 password">
                        <label for="pwd"> <?= i::__('Senha'); ?> </label>
                        <input autocomplete="off" id="pwd" type="password" name="password" v-model="password" />
                        <div class="seePassword" @click="togglePassword('pwd', $event)"></div>
                    </div>

                    <div class="field col-12 password">
                        <label for="pwd-check">
                            <?= i::__('Confirme sua senha'); ?>
                        </label>
                        <input autocomplete="off" id="pwd-check" type="password" name="confirm_password" v-model="confirmPassword" />
                        <div class="seePassword" @click="togglePassword('pwd-check', $event)"></div>
                    </div>

                    <div class="col-12">
                        <password-strongness :password="password"></password-strongness>
                    </div>

                    <VueRecaptcha v-if="configs['google-recaptcha-sitekey']" :sitekey="configs['google-recaptcha-sitekey']" @verify="verifyCaptcha" @expired="expiredCaptcha" class="g-recaptcha col-12"></VueRecaptcha>
                    
                    <button class="col-12 button button--primary button--large button--md" type="submit"> <?= i::__('Continuar') ?> </button>
                </form>
                
                <div v-if="configs.strategies.Google?.visible || configs.strategies.govbr?.visible" class="divider col-12"></div>

                <div v-if="configs.strategies.Google?.visible || configs.strategies.govbr?.visible" class="social-login col-12">
                    <a v-if="configs.strategies.govbr?.visible" class="social-login--button button button--icon button--large button--md govbr" href="<?php echo $app->createUrl('auth', 'govbr') ?>">                                
                        <div class="img"> <img height="16" class="br-sign-in-img" src="<?php $this->asset('img/govbr-white.png'); ?>" /> </div>                                
                        <?= i::__('Entrar com Gov.br') ?>                            
                    </a>                    
                    <a v-if="configs.strategies.Google?.visible" class="social-login--button button button--icon button--large button--md google" href="<?php echo $app->createUrl('auth', 'google') ?>">                                
                        <div class="img"> <img height="16" src="<?php $this->asset('img/g.png'); ?>" /> </div>                                
                        <?= i::__('Entrar com Google') ?>
                    </a>
                </div>
            </div>

            <!-- Terms steps -->
            <div v-show="step==index+1" v-for="(value, name, index) in terms" class="create-account__step grid-12">
                {{index}}
                <label class="title col-12"> {{value.title}} </label>
                <div class="term col-12" v-html="value.text" :id="'term'+index" ref="terms"></div>
                <div class="divider col-12"></div>                
                <button class="col-12 button button--primary button--large button--md" :id="'acceptTerm'+index" @click="nextStep(); acceptTerm(name)"> {{value.buttonText}} </button>
                <button class="col-12 button button--text" @click="cancel()"> <?= i::__('Voltar e excluir minhas informações') ?> </button>
            </div>

            <!-- Creating account -->
            <div v-if="step == totalSteps" class="create-account__created grid-12">
                <div class="col-12 title">
                    <mc-icon name="loading" class="title__icon"></mc-icon>
                    <label class="col-12 title__label"> <?= i::__('Criando seu cadastro') ?> </label>
                </div>
            </div>

        </template>
    </mc-card>
</div>
