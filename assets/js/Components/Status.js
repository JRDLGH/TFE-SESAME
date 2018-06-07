import '../../scss/structure/status.scss';

const STATES = ["waiting","success","not_found","error","invalid"];
const DEFAULT_MSG = {
    error: 'Une erreur est survenue, veuillez contacter l\'administrateur en cas' +
    ' d\'erreur répétée.',
    not_found: 'Aucune correspondance n\'a été trouvé pour votre recherche.',
    invalid: 'Votre recherche est invalide.'
};

class Status
{

    constructor($container = $('.status'),messages = DEFAULT_MSG)
    {
        this.$container = $container;
        this.currentStatus = null;
        this.messages = messages;
    }

    clear()
    {
        if(this.currentStatus){
            this.currentStatus = null;
        }

        this.$container.children('.status-message').html('');
        this.$container.children('i').attr('class','');
    }

    isValid(state)
    {
        let valid = false;
        if(state){
            STATES.forEach((vState) => {
                if(state === vState){
                    valid = true;
                }
            });
        }
        return valid;
    }

    static isError(state)
    {
        return state === 'not_found' || state === 'error' || state === 'invalid';

    }

    set(state,message = null)
    {
        this.clear();
        if(this.isValid(state)){
            this.setCurrent(state);

            // hidePaginationButtons();
            if(!message){
                if(Status.isError(state)){
                    message = this.getMessage(state);
                }
            }

            this.setMessage(message);

            switch (state){
                case 'success': this.$container.children('i').addClass('fa fa-check fa-2x');
                    break;
                case 'waiting':  this.$container.children('i').addClass('fa fa-spinner fa-spin fa-2x');
                    break;
                case 'not_found': this.$container.children('i').addClass('fa fa-times fa-2x red-text');
                    break;
                case 'error': this.$container.children('i').addClass('fa fa-exclamation-triangle fa-2x yellow-text');
                    break;
                case 'invalid': this.$container.children('i').addClass('fa fa-ban fa-2x');
                    break;
            }
        }

    }

    getMessage(state)
    {
        let status_msgs = Object.keys(this.messages);
        let msg = '';

        status_msgs.forEach((status_msg) => {
            if(status_msg === state){
                msg = this.messages[status_msg];
            }
        });

        return msg;
    }

    setCurrent(state)
    {
        this.currentStatus = state;
    }

    setMessage(message)
    {
        this.$container.children('.status-message').html(message);
    }

}

export default Status;