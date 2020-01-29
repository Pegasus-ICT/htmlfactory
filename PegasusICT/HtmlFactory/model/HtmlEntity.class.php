<?php declare( strict_types = 1 );

namespace PegasusICT\HtmlFactory\model;

use PegasusICT\HtmlFactory\HtmlEntityInterface;
use PegasusICT\HtmlFactory\HtmlConstants as HC;
use PegasusICT\PhpHelpers\AttributeException as AE;
use PegasusICT\PhpHelpers\GeneralException as GE;
use PegasusICT\PhpHelpers\TypeException as TE;

abstract class HtmlEntity implements HtmlEntityInterface {
    private $_html='';
    private $_attributes=[];
    private $_children=[];
    protected $_entityType='';

    /**
     * HtmlEntity constructor.
     *
     * @param string $entity
     * @param array  $attributes
     * @param array  $Children
     *
     * @throws \PegasusICT\PhpHelpers\GeneralException
     */
    public function __construct(string $entity, array $attributes=[], array $Children=[] ) {
        if( !in_array($entity, array_keys(HC::ENTITIES['elements'])))
            throw new GE("Illegal entity Type $entity", GE::EXCEPT_ILLEGAL_ARG );
        $this->_entityType = $entity;
        if(!empty($attributes)){foreach($attributes as $attribute=> $value){$this->setAttribute($attribute, $value);}}
    }

    /**
     * Validate url/email/ip/regexp etc...
     * todo: add more validation options
     *
     * @param $variable
     * @param $type     // url/email/ip/regexp
     *
     * @return string|bool
     * @throws \PegasusICT\PhpHelpers\AttributeException
     */
    protected function validate( $variable, $type ) {
        switch( $type ) {
            case "url" :
                $result = filter_var( $variable,FILTER_VALIDATE_URL );
                break;
            case "email" :
                $result = filter_var( $variable,FILTER_VALIDATE_EMAIL );
                break;
            case "ip" :
                $result = filter_var( $variable,FILTER_VALIDATE_IP );
                break;
            case "regexp" :
                $result = filter_var( $variable,FILTER_VALIDATE_REGEXP );
                break;
            default:
                throw new AE( "I have no idea what you want....",
                              AE::EXCEPT_ATTR_ELEMENT_MISMATCH );
        }

        return $result;
    }

    /**
     * @param string $attributeName
     * @param mixed  ...$value
     *
     * @return \PegasusICT\HtmlFactory\model\HtmlEntity
     * @throws \PegasusICT\PhpHelpers\AttributeException
     * @throws \PegasusICT\PhpHelpers\GeneralException
     */
    public function setAttribute(string $attributeName, ...$value):HtmlEntity {
        if( !in_array($attributeName, array_keys(HC::ENTITIES['elements'][$this->_entityType]['attributes'])))
            throw new AttributeException("Unknown attribute: $attributeName");
        if(!is_array($this->_attributes[$attributeName])&&!empty($this->_attributes[$attributeName]))
            throw new AttributeException("Attribute $attributeName already set!",AttributeException::XCPT_ATTR_ERROR);
        $type = HC::ENTITIES['elements'][$this->_entityType]['attributes'][$attributeName]['FieldType'];
        foreach($value as $item) {
            $result = $this->validate($value,$type);
            if($result === false) {
                throw new TypeException("Invalid $type: ".print_r($item,true),TypeException::XCPT_TYPE_ERR);
            }

        }



        return $this;
    }

    /**
     * @param \PegasusICT\HtmlFactory\model\HtmlEntity $child
     *
     * @return \PegasusICT\HtmlFactory\model\HtmlEntity
     *
     * @throws \PegasusICT\HtmlFactory\BaseException
     */
    public function addChild(HtmlEntity $child):HtmlEntity {
        if(!in_array($child,HC::ENTITIES))
            throw new BaseException("Unknown Html Entity",BaseException::XCPT_ILL_ARG);
        $this->_children[] = new $this($child);

        return $this;
    }

    /**
     * @return string
     */
    public function render(): string {
        $this->_html = "<" . $this->_entityType;
        if(!empty($this->_attributes)){
            foreach($this->_attributes as $attributeName => $attributeValue) {
                if(empty($attributeValue))$attributeValue = $attributeName;
                $this->_html .= " $attributeName=\"$attributeValue\"";
            }
        }
        if(in_array($this->_entityType,array_keys(HC::ELEMENTS_SINGLE))) {
            $this->_html .= ">\n";
        } else{
            if(!empty($this->_children)) {
                $lChildren = array_reverse($this->_children);
                foreach($lChildren as $lChild) {
                    $this->_html .= $lChild->render();
                }
            }
            $this->_html .= sprintf("</%s>",$this->_entityType);
        }
        return $this->_html;
    }
}