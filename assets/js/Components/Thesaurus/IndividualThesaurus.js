import Thesaurus from "./Thesaurus";

class IndividualThesaurus extends Thesaurus{
    constructor(source,$container,Paginator = null)
    {
        super(source,$container,Paginator);
    }
}

export default IndividualThesaurus;