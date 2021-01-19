let cardManager = {
    _handler: [],
    default: {
        force: 0.5,
        saturation: 1e2,
        additional: {
            perspective: '600px'
        }
    },
    handle(className, options = this.default){
        options = {...this.default,...options}
        this._handler.push(this._constructor(`.${className}`, className, options))
    },
    _constructor(selector, name, options){
        return {name:name,selector:this._applyStyle(selector,options.additional).mousemove(function(e){
                let xr = -(e.offsetY-$(this).outerHeight()/2)/($(this).outerHeight()/options.saturation)*options.force
                let yr = (e.offsetX-$(this).outerWidth()/2)/($(this).outerWidth()/options.saturation)*options.force
                $(this).css({
                    '--xr': xr+'deg',
                    '--yr': yr+'deg'
                })
            })}
    },
    _applyStyle(selector, options){
        let args = Object.keys(options).map(i=>`${i}(${options[i]})`)
        return $(selector).css({
            transform: args.join(' ')+' rotateX(var(--xr)) rotateY(var(--yr))'
        })
    },
    clear(className){
        if(className===undefined)return this._handler = []
        let index = this._handler.map(i=>i.name).indexOf(className)
        if(index<0)return false
        return this._handler[index].selector.unbind('mousemove'), this._handler.splice(index,1)
    }
}