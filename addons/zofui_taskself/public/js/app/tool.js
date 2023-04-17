function Lottery(id,pos,poselem,fn) {
    this.conId = id;
    this.width = pos.width;
    this.height = pos.height;
    this.elem = document.getElementById(this.conId);
    this.maskCtx = this.elem.getContext('2d');
    this.drawPercentCallback = fn;
    this.poselem = poselem;
    this.isMouseDown = false;
    this.end = false;
}

Lottery.prototype = {
    getTransparentPercent: function(ctx, width, height) {
        var imgData = ctx.getImageData(0, 0, width, height),
            pixles = imgData.data,
            transPixs = [];
        for (var i = 0, j = pixles.length; i < j; i += 4) {
            var a = pixles[i + 3];
            if (a < 128) {
                transPixs.push(i);
            }
        }
        var per =  (transPixs.length / (pixles.length / 4) * 100).toFixed(2);
        return per;
    },
    resizeCanvas: function (canvas, width, height) {
        canvas.width = width;
        canvas.height = height;
        canvas.getContext('2d').clearRect(0, 0, width, height);
    },
    drawPoint: function (x, y) {
        this.maskCtx.beginPath();
        var radgrad = this.maskCtx.createRadialGradient(x, y, 0, x, y, 30);
        radgrad.addColorStop(0, 'rgba(0,0,0,1)');
        radgrad.addColorStop(1, 'rgba(255, 255, 255, 0)');
        this.maskCtx.fillStyle = radgrad;
        this.maskCtx.arc(x, y, 30, 0, Math.PI * 2, true);
        this.maskCtx.fill();
        if ( this.drawPercentCallback && !this.isMouseDown ) {
            this.drawPercentCallback.call(null, this.getTransparentPercent(this.maskCtx, this.width, this.height));
        }
    },
    bindEvent: function () {
        var _this = this;
        var clickEvtName = 'touchstart';
        var moveEvtName = 'touchmove';
    
        this.elem.addEventListener("touchmove", function(e) {
            if (_this.isMouseDown) {
                e.preventDefault();
            }
        }, false);
        this.elem.addEventListener('touchend', function(e) {
            if( _this.end ) return false;
            _this.isMouseDown = false;
            _this.end = true;

            var left = _this.poselem.offsetLeft;
            var top = _this.poselem.offsetTop;

            var x = e.changedTouches[0].clientX - left;
            var y = e.changedTouches[0].clientY - top;
            _this.drawPoint(x, y);

        }, false);

        this.elem.addEventListener(clickEvtName, function (e) {
            _this.isMouseDown = true;
            _this.end = false;
            
            var left = _this.poselem.offsetLeft;
            var top = _this.poselem.offsetTop;

            var x = e.touches[0].clientX - left;
            var y = e.touches[0].clientY - top;
            _this.drawPoint(x, y);

        }, false);

        this.elem.addEventListener(moveEvtName, function (e) {
            if (!_this.isMouseDown) return false;
            var left = _this.poselem.offsetLeft;
            var top = _this.poselem.offsetTop;

            var x = e.touches[0].clientX - left;
            var y = e.touches[0].clientY - top;
            _this.drawPoint(x, y);

            e.preventDefault();
        }, false);
    },
    drawMask: function() {

        this.maskCtx.fillStyle = '#808080';
        this.maskCtx.fillRect(0, 0, this.width, this.height);
        
        this.maskCtx.font = "30px Georgia";
        this.maskCtx.fillStyle = '#fff';
        this.maskCtx.fillText("使劲刮开我", this.width/2-75, this.height/2+10); 

        this.maskCtx.globalCompositeOperation = 'destination-out';
    },
    init: function () {
        
        this.elem.width = this.width;
        this.elem.height = this.height;
        this.drawMask();
        this.bindEvent();
    }
}