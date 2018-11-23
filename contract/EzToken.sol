pragma solidity ^0.4.0;
contract EzToken{
    
    uint256 totalsuply;
    mapping(address=>uint256) public balances;
    mapping(address=>mapping(address=>uint256)) public allowed;
    
    event Transfer(address indexed _from, address indexed _to, uint256 _value);
    event Approval(address indexed _owner, address indexed _spender, uint256 _value);
    
    string public name;
    string public symbol;
    uint8 public decimal;
    
    constructor(
        uint256 _initialAmount,
        string _tokenName,
        string _tokenSymbol,
        uint8 _decimailUnits
    ) public{
        balances[msg.sender]=_initialAmount;
        totalsuply=_initialAmount;
        name = _tokenName;
        symbol = _tokenSymbol;
        decimal = _decimailUnits;
    }
    
    function transfer(address _to, uint256 _value) public returns(bool success){
        require(balances[msg.sender] > _value);
        balances[msg.sender] -= _value;
        balances[_to] +=_value;
        emit Transfer(msg.sender, _to, _value);
        return true;
    }
    
    function balanceOf(address _owner) public view returns(uint256 balance){
        return balances[_owner];
    }
    
    function approval(address _spender, uint256 _value) public returns(bool success){
        allowed[msg.sender][_spender] = _value;
        emit Approval(msg.sender, _spender, _value);
        return true;
    }
    
    function transferFrom(address _from, address _to, uint256 _value) public returns(bool success){
        uint256 allowce = allowed[_from][msg.sender];
        require(balances[_from] > _value && allowce > _value );
        balances[_from] -= _value;
        balances[_to] += _value;
        allowed[_from][msg.sender] -= _value;
        emit Transfer(_from, _to, _value);
        return true;
    }
    
    function allowance(address _owner, address _spender) public view returns(uint256 allowce){
        return allowed[_owner][_spender];
    }
    
}