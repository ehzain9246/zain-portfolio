// SVGPathSeg API polyfill
// https://github.com/progers/pathseg
//
// This is a drop-in replacement for the SVGPathSeg and SVGPathSegList APIs that were removed from
// SVG2 (https://lists.w3.org/Archives/Public/www-svg/2015Jun/0044.html), including the latest spec
// changes which were implemented in Firefox 43 and Chrome 46.

(function () {
    "use strict";
    if (!("SVGPathSeg" in window)) {
        // Spec: http://www.w3.org/TR/SVG11/single-page.html#paths-InterfaceSVGPathSeg
        window.SVGPathSeg = function (type, typeAsLetter, owningPathSegList) {
            this.pathSegType = type;
            this.pathSegTypeAsLetter = typeAsLetter;
            this._owningPathSegList = owningPathSegList;
        }

        window.SVGPathSeg.prototype.classname = "SVGPathSeg";

        window.SVGPathSeg.PATHSEG_UNKNOWN = 0;
        window.SVGPathSeg.PATHSEG_CLOSEPATH = 1;
        window.SVGPathSeg.PATHSEG_MOVETO_ABS = 2;
        window.SVGPathSeg.PATHSEG_MOVETO_REL = 3;
        window.SVGPathSeg.PATHSEG_LINETO_ABS = 4;
        window.SVGPathSeg.PATHSEG_LINETO_REL = 5;
        window.SVGPathSeg.PATHSEG_CURVETO_CUBIC_ABS = 6;
        window.SVGPathSeg.PATHSEG_CURVETO_CUBIC_REL = 7;
        window.SVGPathSeg.PATHSEG_CURVETO_QUADRATIC_ABS = 8;
        window.SVGPathSeg.PATHSEG_CURVETO_QUADRATIC_REL = 9;
        window.SVGPathSeg.PATHSEG_ARC_ABS = 10;
        window.SVGPathSeg.PATHSEG_ARC_REL = 11;
        window.SVGPathSeg.PATHSEG_LINETO_HORIZONTAL_ABS = 12;
        window.SVGPathSeg.PATHSEG_LINETO_HORIZONTAL_REL = 13;
        window.SVGPathSeg.PATHSEG_LINETO_VERTICAL_ABS = 14;
        window.SVGPathSeg.PATHSEG_LINETO_VERTICAL_REL = 15;
        window.SVGPathSeg.PATHSEG_CURVETO_CUBIC_SMOOTH_ABS = 16;
        window.SVGPathSeg.PATHSEG_CURVETO_CUBIC_SMOOTH_REL = 17;
        window.SVGPathSeg.PATHSEG_CURVETO_QUADRATIC_SMOOTH_ABS = 18;
        window.SVGPathSeg.PATHSEG_CURVETO_QUADRATIC_SMOOTH_REL = 19;

        // Notify owning PathSegList on any changes so they can be synchronized back to the path element.
        window.SVGPathSeg.prototype._segmentChanged = function () {
            if (this._owningPathSegList)
                this._owningPathSegList.segmentChanged(this);
        }

        window.SVGPathSegClosePath = function (owningPathSegList) {
            window.SVGPathSeg.call(this, window.SVGPathSeg.PATHSEG_CLOSEPATH, "z", owningPathSegList);
        }
        window.SVGPathSegClosePath.prototype = Object.create(window.SVGPathSeg.prototype);
        window.SVGPathSegClosePath.prototype.toString = function () { return "[object SVGPathSegClosePath]"; }
        window.SVGPathSegClosePath.prototype._asPathString = function () { return this.pathSegTypeAsLetter; }
        window.SVGPathSegClosePath.prototype.clone = function () { return new window.SVGPathSegClosePath(undefined); }

        window.SVGPathSegMovetoAbs = function (owningPathSegList, x, y) {
            window.SVGPathSeg.call(this, window.SVGPathSeg.PATHSEG_MOVETO_ABS, "M", owningPathSegList);
            this._x = x;
            this._y = y;
        }
        window.SVGPathSegMovetoAbs.prototype = Object.create(window.SVGPathSeg.prototype);
        window.SVGPathSegMovetoAbs.prototype.toString = function () { return "[object SVGPathSegMovetoAbs]"; }
        window.SVGPathSegMovetoAbs.prototype._asPathString = function () { return this.pathSegTypeAsLetter + " " + this._x + " " + this._y; }
        window.SVGPathSegMovetoAbs.prototype.clone = function () { return new window.SVGPathSegMovetoAbs(undefined, this._x, this._y); }
        Object.defineProperty(window.SVGPathSegMovetoAbs.prototype, "x", { get: function () { return this._x; }, set: function (x) { this._x = x; this._segmentChanged(); }, enumerable: true });
        Object.defineProperty(window.SVGPathSegMovetoAbs.prototype, "y", { get: function () { return this._y; }, set: function (y) { this._y = y; this._segmentChanged(); }, enumerable: true });

        window.SVGPathSegMovetoRel = function (owningPathSegList, x, y) {
            window.SVGPathSeg.call(this, window.SVGPathSeg.PATHSEG_MOVETO_REL, "m", owningPathSegList);
            this._x = x;
            this._y = y;
        }
        window.SVGPathSegMovetoRel.prototype = Object.create(window.SVGPathSeg.prototype);
        window.SVGPathSegMovetoRel.prototype.toString = function () { return "[object SVGPathSegMovetoRel]"; }
        window.SVGPathSegMovetoRel.prototype._asPathString = function () { return this.pathSegTypeAsLetter + " " + this._x + " " + this._y; }
        window.SVGPathSegMovetoRel.prototype.clone = function () { return new window.SVGPathSegMovetoRel(undefined, this._x, this._y); }
        Object.defineProperty(window.SVGPathSegMovetoRel.prototype, "x", { get: function () { return this._x; }, set: function (x) { this._x = x; this._segmentChanged(); }, enumerable: true });
        Object.defineProperty(window.SVGPathSegMovetoRel.prototype, "y", { get: function () { return this._y; }, set: function (y) { this._y = y; this._segmentChanged(); }, enumerable: true });

        window.SVGPathSegLinetoAbs = function (owningPathSegList, x, y) {
            window.SVGPathSeg.call(this, window.SVGPathSeg.PATHSEG_LINETO_ABS, "L", owningPathSegList);
            this._x = x;
            this._y = y;
        }
        window.SVGPathSegLinetoAbs.prototype = Object.create(window.SVGPathSeg.prototype);
        window.SVGPathSegLinetoAbs.prototype.toString = function () { return "[object SVGPathSegLinetoAbs]"; }
        window.SVGPathSegLinetoAbs.prototype._asPathString = function () { return this.pathSegTypeAsLetter + " " + this._x + " " + this._y; }
        window.SVGPathSegLinetoAbs.prototype.clone = function () { return new window.SVGPathSegLinetoAbs(undefined, this._x, this._y); }
        Object.defineProperty(window.SVGPathSegLinetoAbs.prototype, "x", { get: function () { return this._x; }, set: function (x) { this._x = x; this._segmentChanged(); }, enumerable: true });
        Object.defineProperty(window.SVGPathSegLinetoAbs.prototype, "y", { get: function () { return this._y; }, set: function (y) { this._y = y; this._segmentChanged(); }, enumerable: true });

        window.SVGPathSegLinetoRel = function (owningPathSegList, x, y) {
            window.SVGPathSeg.call(this, window.SVGPathSeg.PATHSEG_LINETO_REL, "l", owningPathSegList);
            this._x = x;
            this._y = y;
        }
        window.SVGPathSegLinetoRel.prototype = Object.create(window.SVGPathSeg.prototype);
        window.SVGPathSegLinetoRel.prototype.toString = function () { return "[object SVGPathSegLinetoRel]"; }
        window.SVGPathSegLinetoRel.prototype._asPathString = function () { return this.pathSegTypeAsLetter + " " + this._x + " " + this._y; }
        window.SVGPathSegLinetoRel.prototype.clone = function () { return new window.SVGPathSegLinetoRel(undefined, this._x, this._y); }
        Object.defineProperty(window.SVGPathSegLinetoRel.prototype, "x", { get: function () { return this._x; }, set: function (x) { this._x = x; this._segmentChanged(); }, enumerable: true });
        Object.defineProperty(window.SVGPathSegLinetoRel.prototype, "y", { get: function () { return this._y; }, set: function (y) { this._y = y; this._segmentChanged(); }, enumerable: true });

        window.SVGPathSegCurvetoCubicAbs = function (owningPathSegList, x, y, x1, y1, x2, y2) {
            window.SVGPathSeg.call(this, window.SVGPathSeg.PATHSEG_CURVETO_CUBIC_ABS, "C", owningPathSegList);
            this._x = x;
            this._y = y;
            this._x1 = x1;
            this._y1 = y1;
            this._x2 = x2;
            this._y2 = y2;
        }
        window.SVGPathSegCurvetoCubicAbs.prototype = Object.create(window.SVGPathSeg.prototype);
        window.SVGPathSegCurvetoCubicAbs.prototype.toString = function () { return "[object SVGPathSegCurvetoCubicAbs]"; }
        window.SVGPathSegCurvetoCubicAbs.prototype._asPathString = function () { return this.pathSegTypeAsLetter + " " + this._x1 + " " + this._y1 + " " + this._x2 + " " + this._y2 + " " + this._x + " " + this._y; }
        window.SVGPathSegCurvetoCubicAbs.prototype.clone = function () { return new window.SVGPathSegCurvetoCubicAbs(undefined, this._x, this._y, this._x1, this._y1, this._x2, this._y2); }
        Object.defineProperty(window.SVGPathSegCurvetoCubicAbs.prototype, "x", { get: function () { return this._x; }, set: function (x) { this._x = x; this._segmentChanged(); }, enumerable: true });
        Object.defineProperty(window.SVGPathSegCurvetoCubicAbs.prototype, "y", { get: function () { return this._y; }, set: function (y) { this._y = y; this._segmentChanged(); }, enumerable: true });
        Object.defineProperty(window.SVGPathSegCurvetoCubicAbs.prototype, "x1", { get: function () { return this._x1; }, set: function (x1) { this._x1 = x1; this._segmentChanged(); }, enumerable: true });
        Object.defineProperty(window.SVGPathSegCurvetoCubicAbs.prototype, "y1", { get: function () { return this._y1; }, set: function (y1) { this._y1 = y1; this._segmentChanged(); }, enumerable: true });
        Object.defineProperty(window.SVGPathSegCurvetoCubicAbs.prototype, "x2", { get: function () { return this._x2; }, set: function (x2) { this._x2 = x2; this._segmentChanged(); }, enumerable: true });
        Object.defineProperty(window.SVGPathSegCurvetoCubicAbs.prototype, "y2", { get: function () { return this._y2; }, set: function (y2) { this._y2 = y2; this._segmentChanged(); }, enumerable: true });

        window.SVGPathSegCurvetoCubicRel = function (owningPathSegList, x, y, x1, y1, x2, y2) {
            window.SVGPathSeg.call(this, window.SVGPathSeg.PATHSEG_CURVETO_CUBIC_REL, "c", owningPathSegList);
            this._x = x;
            this._y = y;
            this._x1 = x1;
            this._y1 = y1;
            this._x2 = x2;
            this._y2 = y2;
        }
        window.SVGPathSegCurvetoCubicRel.prototype = Object.create(window.SVGPathSeg.prototype);
        window.SVGPathSegCurvetoCubicRel.prototype.toString = function () { return "[object SVGPathSegCurvetoCubicRel]"; }
        window.SVGPathSegCurvetoCubicRel.prototype._asPathString = function () { return this.pathSegTypeAsLetter + " " + this._x1 + " " + this._y1 + " " + this._x2 + " " + this._y2 + " " + this._x + " " + this._y; }
        window.SVGPathSegCurvetoCubicRel.prototype.clone = function () { return new window.SVGPathSegCurvetoCubicRel(undefined, this._x, this._y, this._x1, this._y1, this._x2, this._y2); }
        Object.defineProperty(window.SVGPathSegCurvetoCubicRel.prototype, "x", { get: function () { return this._x; }, set: function (x) { this._x = x; this._segmentChanged(); }, enumerable: true });
        Object.defineProperty(window.SVGPathSegCurvetoCubicRel.prototype, "y", { get: function () { return this._y; }, set: function (y) { this._y = y; this._segmentChanged(); }, enumerable: true });
        Object.defineProperty(window.SVGPathSegCurvetoCubicRel.prototype, "x1", { get: function () { return this._x1; }, set: function (x1) { this._x1 = x1; this._segmentChanged(); }, enumerable: true });
        Object.defineProperty(window.SVGPathSegCurvetoCubicRel.prototype, "y1", { get: function () { return this._y1; }, set: function (y1) { this._y1 = y1; this._segmentChanged(); }, enumerable: true });
        Object.defineProperty(window.SVGPathSegCurvetoCubicRel.prototype, "x2", { get: function () { return this._x2; }, set: function (x2) { this._x2 = x2; this._segmentChanged(); }, enumerable: true });
        Object.defineProperty(window.SVGPathSegCurvetoCubicRel.prototype, "y2", { get: function () { return this._y2; }, set: function (y2) { this._y2 = y2; this._segmentChanged(); }, enumerable: true });

        window.SVGPathSegCurvetoQuadraticAbs = function (owningPathSegList, x, y, x1, y1) {
            window.SVGPathSeg.call(this, window.SVGPathSeg.PATHSEG_CURVETO_QUADRATIC_ABS, "Q", owningPathSegList);
            this._x = x;
            this._y = y;
            this._x1 = x1;
            this._y1 = y1;
        }
        window.SVGPathSegCurvetoQuadraticAbs.prototype = Object.create(window.SVGPathSeg.prototype);
        window.SVGPathSegCurvetoQuadraticAbs.prototype.toString = function () { return "[object SVGPathSegCurvetoQuadraticAbs]"; }
        window.SVGPathSegCurvetoQuadraticAbs.prototype._asPathString = function () { return this.pathSegTypeAsLetter + " " + this._x1 + " " + this._y1 + " " + this._x + " " + this._y; }
        window.SVGPathSegCurvetoQuadraticAbs.prototype.clone = function () { return new window.SVGPathSegCurvetoQuadraticAbs(undefined, this._x, this._y, this._x1, this._y1); }
        Object.defineProperty(window.SVGPathSegCurvetoQuadraticAbs.prototype, "x", { get: function () { return this._x; }, set: function (x) { this._x = x; this._segmentChanged(); }, enumerable: true });
        Object.defineProperty(window.SVGPathSegCurvetoQuadraticAbs.prototype, "y", { get: function () { return this._y; }, set: function (y) { this._y = y; this._segmentChanged(); }, enumerable: true });
        Object.defineProperty(window.SVGPathSegCurvetoQuadraticAbs.prototype, "x1", { get: function () { return this._x1; }, set: function (x1) { this._x1 = x1; this._segmentChanged(); }, enumerable: true });
        Object.defineProperty(window.SVGPathSegCurvetoQuadraticAbs.prototype, "y1", { get: function () { return this._y1; }, set: function (y1) { this._y1 = y1; this._segmentChanged(); }, enumerable: true });

        window.SVGPathSegCurvetoQuadraticRel = function (owningPathSegList, x, y, x1, y1) {
            window.SVGPathSeg.call(this, window.SVGPathSeg.PATHSEG_CURVETO_QUADRATIC_REL, "q", owningPathSegList);
            this._x = x;
            this._y = y;
            this._x1 = x1;
            this._y1 = y1;
        }
        window.SVGPathSegCurvetoQuadraticRel.prototype = Object.create(window.SVGPathSeg.prototype);
        window.SVGPathSegCurvetoQuadraticRel.prototype.toString = function () { return "[object SVGPathSegCurvetoQuadraticRel]"; }
        window.SVGPathSegCurvetoQuadraticRel.prototype._asPathString = function () { return this.pathSegTypeAsLetter + " " + this._x1 + " " + this._y1 + " " + this._x + " " + this._y; }
        window.SVGPathSegCurvetoQuadraticRel.prototype.clone = function () { return new window.SVGPathSegCurvetoQuadraticRel(undefined, this._x, this._y, this._x1, this._y1); }
        Object.defineProperty(window.SVGPathSegCurvetoQuadraticRel.prototype, "x", { get: function () { return this._x; }, set: function (x) { this._x = x; this._segmentChanged(); }, enumerable: true });
        Object.defineProperty(window.SVGPathSegCurvetoQuadraticRel.prototype, "y", { get: function () { return this._y; }, set: function (y) { this._y = y; this._segmentChanged(); }, enumerable: true });
        Object.defineProperty(window.SVGPathSegCurvetoQuadraticRel.prototype, "x1", { get: function () { return this._x1; }, set: function (x1) { this._x1 = x1; this._segmentChanged(); }, enumerable: true });
        Object.defineProperty(window.SVGPathSegCurvetoQuadraticRel.prototype, "y1", { get: function () { return this._y1; }, set: function (y1) { this._y1 = y1; this._segmentChanged(); }, enumerable: true });

        window.SVGPathSegArcAbs = function (owningPathSegList, x, y, r1, r2, angle, largeArcFlag, sweepFlag) {
            window.SVGPathSeg.call(this, window.SVGPathSeg.PATHSEG_ARC_ABS, "A", owningPathSegList);
            this._x = x;
            this._y = y;
            this._r1 = r1;
            this._r2 = r2;
            this._angle = angle;
            this._largeArcFlag = largeArcFlag;
            this._sweepFlag = sweepFlag;
        }
        window.SVGPathSegArcAbs.prototype = Object.create(window.SVGPathSeg.prototype);
        window.SVGPathSegArcAbs.prototype.toString = function () { return "[object SVGPathSegArcAbs]"; }
        window.SVGPathSegArcAbs.prototype._asPathString = function () { return this.pathSegTypeAsLetter + " " + this._r1 + " " + this._r2 + " " + this._angle + " " + (this._largeArcFlag ? "1" : "0") + " " + (this._sweepFlag ? "1" : "0") + " " + this._x + " " + this._y; }
        window.SVGPathSegArcAbs.prototype.clone = function () { return new window.SVGPathSegArcAbs(undefined, this._x, this._y, this._r1, this._r2, this._angle, this._largeArcFlag, this._sweepFlag); }
        Object.defineProperty(window.SVGPathSegArcAbs.prototype, "x", { get: function () { return this._x; }, set: function (x) { this._x = x; this._segmentChanged(); }, enumerable: true });
        Object.defineProperty(window.SVGPathSegArcAbs.prototype, "y", { get: function () { return this._y; }, set: function (y) { this._y = y; this._segmentChanged(); }, enumerable: true });
        Object.defineProperty(window.SVGPathSegArcAbs.prototype, "r1", { get: function () { return this._r1; }, set: function (r1) { this._r1 = r1; this._segmentChanged(); }, enumerable: true });
        Object.defineProperty(window.SVGPathSegArcAbs.prototype, "r2", { get: function () { return this._r2; }, set: function (r2) { this._r2 = r2; this._segmentChanged(); }, enumerable: true });
        Object.defineProperty(window.SVGPathSegArcAbs.prototype, "angle", { get: function () { return this._angle; }, set: function (angle) { this._angle = angle; this._segmentChanged(); }, enumerable: true });
        Object.defineProperty(window.SVGPathSegArcAbs.prototype, "largeArcFlag", { get: function () { return this._largeArcFlag; }, set: function (largeArcFlag) { this._largeArcFlag = largeArcFlag; this._segmentChanged(); }, enumerable: true });
        Object.defineProperty(window.SVGPathSegArcAbs.prototype, "sweepFlag", { get: function () { return this._sweepFlag; }, set: function (sweepFlag) { this._sweepFlag = sweepFlag; this._segmentChanged(); }, enumerable: true });

        window.SVGPathSegArcRel = function (owningPathSegList, x, y, r1, r2, angle, largeArcFlag, sweepFlag) {
            window.SVGPathSeg.call(this, window.SVGPathSeg.PATHSEG_ARC_REL, "a", owningPathSegList);
            this._x = x;
            this._y = y;
            this._r1 = r1;
            this._r2 = r2;
            this._angle = angle;
            this._largeArcFlag = largeArcFlag;
            this._sweepFlag = sweepFlag;
        }
        window.SVGPathSegArcRel.prototype = Object.create(window.SVGPathSeg.prototype);
        window.SVGPathSegArcRel.prototype.toString = function () { return "[object SVGPathSegArcRel]"; }
        window.SVGPathSegArcRel.prototype._asPathString = function () { return this.pathSegTypeAsLetter + " " + this._r1 + " " + this._r2 + " " + this._angle + " " + (this._largeArcFlag ? "1" : "0") + " " + (this._sweepFlag ? "1" : "0") + " " + this._x + " " + this._y; }
        window.SVGPathSegArcRel.prototype.clone = function () { return new window.SVGPathSegArcRel(undefined, this._x, this._y, this._r1, this._r2, this._angle, this._largeArcFlag, this._sweepFlag); }
        Object.defineProperty(window.SVGPathSegArcRel.prototype, "x", { get: function () { return this._x; }, set: function (x) { this._x = x; this._segmentChanged(); }, enumerable: true });
        Object.defineProperty(window.SVGPathSegArcRel.prototype, "y", { get: function () { return this._y; }, set: function (y) { this._y = y; this._segmentChanged(); }, enumerable: true });
        Object.defineProperty(window.SVGPathSegArcRel.prototype, "r1", { get: function () { return this._r1; }, set: function (r1) { this._r1 = r1; this._segmentChanged(); }, enumerable: true });
        Object.defineProperty(window.SVGPathSegArcRel.prototype, "r2", { get: function () { return this._r2; }, set: function (r2) { this._r2 = r2; this._segmentChanged(); }, enumerable: true });
        Object.defineProperty(window.SVGPathSegArcRel.prototype, "angle", { get: function () { return this._angle; }, set: function (angle) { this._angle = angle; this._segmentChanged(); }, enumerable: true });
        Object.defineProperty(window.SVGPathSegArcRel.prototype, "largeArcFlag", { get: function () { return this._largeArcFlag; }, set: function (largeArcFlag) { this._largeArcFlag = largeArcFlag; this._segmentChanged(); }, enumerable: true });
        Object.defineProperty(window.SVGPathSegArcRel.prototype, "sweepFlag", { get: function () { return this._sweepFlag; }, set: function (sweepFlag) { this._sweepFlag = sweepFlag; this._segmentChanged(); }, enumerable: true });

        window.SVGPathSegLinetoHorizontalAbs = function (owningPathSegList, x) {
            window.SVGPathSeg.call(this, window.SVGPathSeg.PATHSEG_LINETO_HORIZONTAL_ABS, "H", owningPathSegList);
            this._x = x;
        }
        window.SVGPathSegLinetoHorizontalAbs.prototype = Object.create(window.SVGPathSeg.prototype);
        window.SVGPathSegLinetoHorizontalAbs.prototype.toString = function () { return "[object SVGPathSegLinetoHorizontalAbs]"; }
        window.SVGPathSegLinetoHorizontalAbs.prototype._asPathString = function () { return this.pathSegTypeAsLetter + " " + this._x; }
        window.SVGPathSegLinetoHorizontalAbs.prototype.clone = function () { return new window.SVGPathSegLinetoHorizontalAbs(undefined, this._x); }
        Object.defineProperty(window.SVGPathSegLinetoHorizontalAbs.prototype, "x", { get: function () { return this._x; }, set: function (x) { this._x = x; this._segmentChanged(); }, enumerable: true });

        window.SVGPathSegLinetoHorizontalRel = function (owningPathSegList, x) {
            window.SVGPathSeg.call(this, window.SVGPathSeg.PATHSEG_LINETO_HORIZONTAL_REL, "h", owningPathSegList);
            this._x = x;
        }
        window.SVGPathSegLinetoHorizontalRel.prototype = Object.create(window.SVGPathSeg.prototype);
        window.SVGPathSegLinetoHorizontalRel.prototype.toString = function () { return "[object SVGPathSegLinetoHorizontalRel]"; }
        window.SVGPathSegLinetoHorizontalRel.prototype._asPathString = function () { return this.pathSegTypeAsLetter + " " + this._x; }
        window.SVGPathSegLinetoHorizontalRel.prototype.clone = function () { return new window.SVGPathSegLinetoHorizontalRel(undefined, this._x); }
        Object.defineProperty(window.SVGPathSegLinetoHorizontalRel.prototype, "x", { get: function () { return this._x; }, set: function (x) { this._x = x; this._segmentChanged(); }, enumerable: true });

        window.SVGPathSegLinetoVerticalAbs = function (owningPathSegList, y) {
            window.SVGPathSeg.call(this, window.SVGPathSeg.PATHSEG_LINETO_VERTICAL_ABS, "V", owningPathSegList);
            this._y = y;
        }
        window.SVGPathSegLinetoVerticalAbs.prototype = Object.create(window.SVGPathSeg.prototype);
        window.SVGPathSegLinetoVerticalAbs.prototype.toString = function () { return "[object SVGPathSegLinetoVerticalAbs]"; }
        window.SVGPathSegLinetoVerticalAbs.prototype._asPathString = function () { return this.pathSegTypeAsLetter + " " + this._y; }
        window.SVGPathSegLinetoVerticalAbs.prototype.clone = function () { return new window.SVGPathSegLinetoVerticalAbs(undefined, this._y); }
        Object.defineProperty(window.SVGPathSegLinetoVerticalAbs.prototype, "y", { get: function () { return this._y; }, set: function (y) { this._y = y; this._segmentChanged(); }, enumerable: true });

        window.SVGPathSegLinetoVerticalRel = function (owningPathSegList, y) {
            window.SVGPathSeg.call(this, window.SVGPathSeg.PATHSEG_LINETO_VERTICAL_REL, "v", owningPathSegList);
            this._y = y;
        }
        window.SVGPathSegLinetoVerticalRel.prototype = Object.create(window.SVGPathSeg.prototype);
        window.SVGPathSegLinetoVerticalRel.prototype.toString = function () { return "[object SVGPathSegLinetoVerticalRel]"; }
        window.SVGPathSegLinetoVerticalRel.prototype._asPathString = function () { return this.pathSegTypeAsLetter + " " + this._y; }
        window.SVGPathSegLinetoVerticalRel.prototype.clone = function () { return new window.SVGPathSegLinetoVerticalRel(undefined, this._y); }
        Object.defineProperty(window.SVGPathSegLinetoVerticalRel.prototype, "y", { get: function () { return this._y; }, set: function (y) { this._y = y; this._segmentChanged(); }, enumerable: true });

        window.SVGPathSegCurvetoCubicSmoothAbs = function (owningPathSegList, x, y, x2, y2) {
            window.SVGPathSeg.call(this, window.SVGPathSeg.PATHSEG_CURVETO_CUBIC_SMOOTH_ABS, "S", owningPathSegList);
            this._x = x;
            this._y = y;
            this._x2 = x2;
            this._y2 = y2;
        }
        window.SVGPathSegCurvetoCubicSmoothAbs.prototype = Object.create(window.SVGPathSeg.prototype);
        window.SVGPathSegCurvetoCubicSmoothAbs.prototype.toString = function () { return "[object SVGPathSegCurvetoCubicSmoothAbs]"; }
        window.SVGPathSegCurvetoCubicSmoothAbs.prototype._asPathString = function () { return this.pathSegTypeAsLetter + " " + this._x2 + " " + this._y2 + " " + this._x + " " + this._y; }
        window.SVGPathSegCurvetoCubicSmoothAbs.prototype.clone = function () { return new window.SVGPathSegCurvetoCubicSmoothAbs(undefined, this._x, this._y, this._x2, this._y2); }
        Object.defineProperty(window.SVGPathSegCurvetoCubicSmoothAbs.prototype, "x", { get: function () { return this._x; }, set: function (x) { this._x = x; this._segmentChanged(); }, enumerable: true });
        Object.defineProperty(window.SVGPathSegCurvetoCubicSmoothAbs.prototype, "y", { get: function () { return this._y; }, set: function (y) { this._y = y; this._segmentChanged(); }, enumerable: true });
        Object.defineProperty(window.SVGPathSegCurvetoCubicSmoothAbs.prototype, "x2", { get: function () { return this._x2; }, set: function (x2) { this._x2 = x2; this._segmentChanged(); }, enumerable: true });
        Object.defineProperty(window.SVGPathSegCurvetoCubicSmoothAbs.prototype, "y2", { get: function () { return this._y2; }, set: function (y2) { this._y2 = y2; this._segmentChanged(); }, enumerable: true });

        window.SVGPathSegCurvetoCubicSmoothRel = function (owningPathSegList, x, y, x2, y2) {
            window.SVGPathSeg.call(this, window.SVGPathSeg.PATHSEG_CURVETO_CUBIC_SMOOTH_REL, "s", owningPathSegList);
            this._x = x;
            this._y = y;
            this._x2 = x2;
            this._y2 = y2;
        }
        window.SVGPathSegCurvetoCubicSmoothRel.prototype = Object.create(window.SVGPathSeg.prototype);
        window.SVGPathSegCurvetoCubicSmoothRel.prototype.toString = function () { return "[object SVGPathSegCurvetoCubicSmoothRel]"; }
        window.SVGPathSegCurvetoCubicSmoothRel.prototype._asPathString = function () { return this.pathSegTypeAsLetter + " " + this._x2 + " " + this._y2 + " " + this._x + " " + this._y; }
        window.SVGPathSegCurvetoCubicSmoothRel.prototype.clone = function () { return new window.SVGPathSegCurvetoCubicSmoothRel(undefined, this._x, this._y, this._x2, this._y2); }
        Object.defineProperty(window.SVGPathSegCurvetoCubicSmoothRel.prototype, "x", { get: function () { return this._x; }, set: function (x) { this._x = x; this._segmentChanged(); }, enumerable: true });
        Object.defineProperty(window.SVGPathSegCurvetoCubicSmoothRel.prototype, "y", { get: function () { return this._y; }, set: function (y) { this._y = y; this._segmentChanged(); }, enumerable: true });
        Object.defineProperty(window.SVGPathSegCurvetoCubicSmoothRel.prototype, "x2", { get: function () { return this._x2; }, set: function (x2) { this._x2 = x2; this._segmentChanged(); }, enumerable: true });
        Object.defineProperty(window.SVGPathSegCurvetoCubicSmoothRel.prototype, "y2", { get: function () { return this._y2; }, set: function (y2) { this._y2 = y2; this._segmentChanged(); }, enumerable: true });

        window.SVGPathSegCurvetoQuadraticSmoothAbs = function (owningPathSegList, x, y) {
            window.SVGPathSeg.call(this, window.SVGPathSeg.PATHSEG_CURVETO_QUADRATIC_SMOOTH_ABS, "T", owningPathSegList);
            this._x = x;
            this._y = y;
        }
        window.SVGPathSegCurvetoQuadraticSmoothAbs.prototype = Object.create(window.SVGPathSeg.prototype);
        window.SVGPathSegCurvetoQuadraticSmoothAbs.prototype.toString = function () { return "[object SVGPathSegCurvetoQuadraticSmoothAbs]"; }
        window.SVGPathSegCurvetoQuadraticSmoothAbs.prototype._asPathString = function () { return this.pathSegTypeAsLetter + " " + this._x + " " + this._y; }
        window.SVGPathSegCurvetoQuadraticSmoothAbs.prototype.clone = function () { return new window.SVGPathSegCurvetoQuadraticSmoothAbs(undefined, this._x, this._y); }
        Object.defineProperty(window.SVGPathSegCurvetoQuadraticSmoothAbs.prototype, "x", { get: function () { return this._x; }, set: function (x) { this._x = x; this._segmentChanged(); }, enumerable: true });
        Object.defineProperty(window.SVGPathSegCurvetoQuadraticSmoothAbs.prototype, "y", { get: function () { return this._y; }, set: function (y) { this._y = y; this._segmentChanged(); }, enumerable: true });

        window.SVGPathSegCurvetoQuadraticSmoothRel = function (owningPathSegList, x, y) {
            window.SVGPathSeg.call(this, window.SVGPathSeg.PATHSEG_CURVETO_QUADRATIC_SMOOTH_REL, "t", owningPathSegList);
            this._x = x;
            this._y = y;
        }
        window.SVGPathSegCurvetoQuadraticSmoothRel.prototype = Object.create(window.SVGPathSeg.prototype);
        window.SVGPathSegCurvetoQuadraticSmoothRel.prototype.toString = function () { return "[object SVGPathSegCurvetoQuadraticSmoothRel]"; }
        window.SVGPathSegCurvetoQuadraticSmoothRel.prototype._asPathString = function () { return this.pathSegTypeAsLetter + " " + this._x + " " + this._y; }
        window.SVGPathSegCurvetoQuadraticSmoothRel.prototype.clone = function () { return new window.SVGPathSegCurvetoQuadraticSmoothRel(undefined, this._x, this._y); }
        Object.defineProperty(window.SVGPathSegCurvetoQuadraticSmoothRel.prototype, "x", { get: function () { return this._x; }, set: function (x) { this._x = x; this._segmentChanged(); }, enumerable: true });
        Object.defineProperty(window.SVGPathSegCurvetoQuadraticSmoothRel.prototype, "y", { get: function () { return this._y; }, set: function (y) { this._y = y; this._segmentChanged(); }, enumerable: true });

        // Add createSVGPathSeg* functions to window.SVGPathElement.
        // Spec: http://www.w3.org/TR/SVG11/single-page.html#paths-Interfacewindow.SVGPathElement.
        window.SVGPathElement.prototype.createSVGPathSegClosePath = function () { return new window.SVGPathSegClosePath(undefined); }
        window.SVGPathElement.prototype.createSVGPathSegMovetoAbs = function (x, y) { return new window.SVGPathSegMovetoAbs(undefined, x, y); }
        window.SVGPathElement.prototype.createSVGPathSegMovetoRel = function (x, y) { return new window.SVGPathSegMovetoRel(undefined, x, y); }
        window.SVGPathElement.prototype.createSVGPathSegLinetoAbs = function (x, y) { return new window.SVGPathSegLinetoAbs(undefined, x, y); }
        window.SVGPathElement.prototype.createSVGPathSegLinetoRel = function (x, y) { return new window.SVGPathSegLinetoRel(undefined, x, y); }
        window.SVGPathElement.prototype.createSVGPathSegCurvetoCubicAbs = function (x, y, x1, y1, x2, y2) { return new window.SVGPathSegCurvetoCubicAbs(undefined, x, y, x1, y1, x2, y2); }
        window.SVGPathElement.prototype.createSVGPathSegCurvetoCubicRel = function (x, y, x1, y1, x2, y2) { return new window.SVGPathSegCurvetoCubicRel(undefined, x, y, x1, y1, x2, y2); }
        window.SVGPathElement.prototype.createSVGPathSegCurvetoQuadraticAbs = function (x, y, x1, y1) { return new window.SVGPathSegCurvetoQuadraticAbs(undefined, x, y, x1, y1); }
        window.SVGPathElement.prototype.createSVGPathSegCurvetoQuadraticRel = function (x, y, x1, y1) { return new window.SVGPathSegCurvetoQuadraticRel(undefined, x, y, x1, y1); }
        window.SVGPathElement.prototype.createSVGPathSegArcAbs = function (x, y, r1, r2, angle, largeArcFlag, sweepFlag) { return new window.SVGPathSegArcAbs(undefined, x, y, r1, r2, angle, largeArcFlag, sweepFlag); }
        window.SVGPathElement.prototype.createSVGPathSegArcRel = function (x, y, r1, r2, angle, largeArcFlag, sweepFlag) { return new window.SVGPathSegArcRel(undefined, x, y, r1, r2, angle, largeArcFlag, sweepFlag); }
        window.SVGPathElement.prototype.createSVGPathSegLinetoHorizontalAbs = function (x) { return new window.SVGPathSegLinetoHorizontalAbs(undefined, x); }
        window.SVGPathElement.prototype.createSVGPathSegLinetoHorizontalRel = function (x) { return new window.SVGPathSegLinetoHorizontalRel(undefined, x); }
        window.SVGPathElement.prototype.createSVGPathSegLinetoVerticalAbs = function (y) { return new window.SVGPathSegLinetoVerticalAbs(undefined, y); }
        window.SVGPathElement.prototype.createSVGPathSegLinetoVerticalRel = function (y) { return new window.SVGPathSegLinetoVerticalRel(undefined, y); }
        window.SVGPathElement.prototype.createSVGPathSegCurvetoCubicSmoothAbs = function (x, y, x2, y2) { return new window.SVGPathSegCurvetoCubicSmoothAbs(undefined, x, y, x2, y2); }
        window.SVGPathElement.prototype.createSVGPathSegCurvetoCubicSmoothRel = function (x, y, x2, y2) { return new window.SVGPathSegCurvetoCubicSmoothRel(undefined, x, y, x2, y2); }
        window.SVGPathElement.prototype.createSVGPathSegCurvetoQuadraticSmoothAbs = function (x, y) { return new window.SVGPathSegCurvetoQuadraticSmoothAbs(undefined, x, y); }
        window.SVGPathElement.prototype.createSVGPathSegCurvetoQuadraticSmoothRel = function (x, y) { return new window.SVGPathSegCurvetoQuadraticSmoothRel(undefined, x, y); }

        if (!("getPathSegAtLength" in window.SVGPathElement.prototype)) {
            // Add getPathSegAtLength to SVGPathElement.
            // Spec: https://www.w3.org/TR/SVG11/single-page.html#paths-__svg__SVGPathElement__getPathSegAtLength
            // This polyfill requires SVGPathElement.getTotalLength to implement the distance-along-a-path algorithm.
            window.SVGPathElement.prototype.getPathSegAtLength = function (distance) {
                if (distance === undefined || !isFinite(distance))
                    throw "Invalid arguments.";

                var measurementElement = document.createElementNS("http://www.w3.org/2000/svg", "path");
                measurementElement.setAttribute("d", this.getAttribute("d"));
                var lastPathSegment = measurementElement.pathSegList.numberOfItems - 1;

                // If the path is empty, return 0.
                if (lastPathSegment <= 0)
                    return 0;

                do {
                    measurementElement.pathSegList.removeItem(lastPathSegment);
                    if (distance > measurementElement.getTotalLength())
                        break;
                    lastPathSegment--;
                } while (lastPathSegment > 0);
                return lastPathSegment;
            }
        }
    }

    // Checking for SVGPathSegList in window checks for the case of an implementation without the
    // SVGPathSegList API.
    // The second check for appendItem is specific to Firefox 59+ which removed only parts of the
    // SVGPathSegList API (e.g., appendItem). In this case we need to re-implement the entire API
    // so the polyfill data (i.e., _list) is used throughout.
    if (!("SVGPathSegList" in window) || !("appendItem" in window.SVGPathSegList.prototype)) {
        // Spec: http://www.w3.org/TR/SVG11/single-page.html#paths-InterfaceSVGPathSegList
        window.SVGPathSegList = function (pathElement) {
            this._pathElement = pathElement;
            this._list = this._parsePath(this._pathElement.getAttribute("d"));

            // Use a MutationObserver to catch changes to the path's "d" attribute.
            this._mutationObserverConfig = { "attributes": true, "attributeFilter": ["d"] };
            this._pathElementMutationObserver = new MutationObserver(this._updateListFromPathMutations.bind(this));
            this._pathElementMutationObserver.observe(this._pathElement, this._mutationObserverConfig);
        }

        window.SVGPathSegList.prototype.classname = "SVGPathSegList";

        Object.defineProperty(window.SVGPathSegList.prototype, "numberOfItems", {
            get: function () {
                this._checkPathSynchronizedToList();
                return this._list.length;
            },
            enumerable: true
        });

        // Add the pathSegList accessors to window.SVGPathElement.
        // Spec: http://www.w3.org/TR/SVG11/single-page.html#paths-InterfaceSVGAnimatedPathData
        Object.defineProperty(window.SVGPathElement.prototype, "pathSegList", {
            get: function () {
                if (!this._pathSegList)
                    this._pathSegList = new window.SVGPathSegList(this);
                return this._pathSegList;
            },
            enumerable: true
        });
        // FIXME: The following are not implemented and simply return window.SVGPathElement.pathSegList.
        Object.defineProperty(window.SVGPathElement.prototype, "normalizedPathSegList", { get: function () { return this.pathSegList; }, enumerable: true });
        Object.defineProperty(window.SVGPathElement.prototype, "animatedPathSegList", { get: function () { return this.pathSegList; }, enumerable: true });
        Object.defineProperty(window.SVGPathElement.prototype, "animatedNormalizedPathSegList", { get: function () { return this.pathSegList; }, enumerable: true });

        // Process any pending mutations to the path element and update the list as needed.
        // This should be the first call of all public functions and is needed because
        // MutationObservers are not synchronous so we can have pending asynchronous mutations.
        window.SVGPathSegList.prototype._checkPathSynchronizedToList = function () {
            this._updateListFromPathMutations(this._pathElementMutationObserver.takeRecords());
        }

        window.SVGPathSegList.prototype._updateListFromPathMutations = function (mutationRecords) {
            if (!this._pathElement)
                return;
            var hasPathMutations = false;
            mutationRecords.forEach(function (record) {
                if (record.attributeName == "d")
                    hasPathMutations = true;
            });
            if (hasPathMutations)
                this._list = this._parsePath(this._pathElement.getAttribute("d"));
        }

        // Serialize the list and update the path's 'd' attribute.
        window.SVGPathSegList.prototype._writeListToPath = function () {
            this._pathElementMutationObserver.disconnect();
            this._pathElement.setAttribute("d", window.SVGPathSegList._pathSegArrayAsString(this._list));
            this._pathElementMutationObserver.observe(this._pathElement, this._mutationObserverConfig);
        }

        // When a path segment changes the list needs to be synchronized back to the path element.
        window.SVGPathSegList.prototype.segmentChanged = function (pathSeg) {
            this._writeListToPath();
        }

        window.SVGPathSegList.prototype.clear = function () {
            this._checkPathSynchronizedToList();

            this._list.forEach(function (pathSeg) {
                pathSeg._owningPathSegList = null;
            });
            this._list = [];
            this._writeListToPath();
        }

        window.SVGPathSegList.prototype.initialize = function (newItem) {
            this._checkPathSynchronizedToList();

            this._list = [newItem];
            newItem._owningPathSegList = this;
            this._writeListToPath();
            return newItem;
        }

        window.SVGPathSegList.prototype._checkValidIndex = function (index) {
            if (isNaN(index) || index < 0 || index >= this.numberOfItems)
                throw "INDEX_SIZE_ERR";
        }

        window.SVGPathSegList.prototype.getItem = function (index) {
            this._checkPathSynchronizedToList();

            this._checkValidIndex(index);
            return this._list[index];
        }

        window.SVGPathSegList.prototype.insertItemBefore = function (newItem, index) {
            this._checkPathSynchronizedToList();

            // Spec: If the index is greater than or equal to numberOfItems, then the new item is appended to the end of the list.
            if (index > this.numberOfItems)
                index = this.numberOfItems;
            if (newItem._owningPathSegList) {
                // SVG2 spec says to make a copy.
                newItem = newItem.clone();
            }
            this._list.splice(index, 0, newItem);
            newItem._owningPathSegList = this;
            this._writeListToPath();
            return newItem;
        }

        window.SVGPathSegList.prototype.replaceItem = function (newItem, index) {
            this._checkPathSynchronizedToList();

            if (newItem._owningPathSegList) {
                // SVG2 spec says to make a copy.
                newItem = newItem.clone();
            }
            this._checkValidIndex(index);
            this._list[index] = newItem;
            newItem._owningPathSegList = this;
            this._writeListToPath();
            return newItem;
        }

        window.SVGPathSegList.prototype.removeItem = function (index) {
            this._checkPathSynchronizedToList();

            this._checkValidIndex(index);
            var item = this._list[index];
            this._list.splice(index, 1);
            this._writeListToPath();
            return item;
        }

        window.SVGPathSegList.prototype.appendItem = function (newItem) {
            this._checkPathSynchronizedToList();

            if (newItem._owningPathSegList) {
                // SVG2 spec says to make a copy.
                newItem = newItem.clone();
            }
            this._list.push(newItem);
            newItem._owningPathSegList = this;
            // TODO: Optimize this to just append to the existing attribute.
            this._writeListToPath();
            return newItem;
        }

        window.SVGPathSegList._pathSegArrayAsString = function (pathSegArray) {
            var string = "";
            var first = true;
            pathSegArray.forEach(function (pathSeg) {
                if (first) {
                    first = false;
                    string += pathSeg._asPathString();
                } else {
                    string += " " + pathSeg._asPathString();
                }
            });
            return string;
        }

        // This closely follows SVGPathParser::parsePath from Source/core/svg/SVGPathParser.cpp.
        window.SVGPathSegList.prototype._parsePath = function (string) {
            if (!string || string.length == 0)
                return [];

            var owningPathSegList = this;

            var Builder = function () {
                this.pathSegList = [];
            }

            Builder.prototype.appendSegment = function (pathSeg) {
                this.pathSegList.push(pathSeg);
            }

            var Source = function (string) {
                this._string = string;
                this._currentIndex = 0;
                this._endIndex = this._string.length;
                this._previousCommand = window.SVGPathSeg.PATHSEG_UNKNOWN;

                this._skipOptionalSpaces();
            }

            Source.prototype._isCurrentSpace = function () {
                var character = this._string[this._currentIndex];
                return character <= " " && (character == " " || character == "\n" || character == "\t" || character == "\r" || character == "\f");
            }

            Source.prototype._skipOptionalSpaces = function () {
                while (this._currentIndex < this._endIndex && this._isCurrentSpace())
                    this._currentIndex++;
                return this._currentIndex < this._endIndex;
            }

            Source.prototype._skipOptionalSpacesOrDelimiter = function () {
                if (this._currentIndex < this._endIndex && !this._isCurrentSpace() && this._string.charAt(this._currentIndex) != ",")
                    return false;
                if (this._skipOptionalSpaces()) {
                    if (this._currentIndex < this._endIndex && this._string.charAt(this._currentIndex) == ",") {
                        this._currentIndex++;
                        this._skipOptionalSpaces();
                    }
                }
                return this._currentIndex < this._endIndex;
            }

            Source.prototype.hasMoreData = function () {
                return this._currentIndex < this._endIndex;
            }

            Source.prototype.peekSegmentType = function () {
                var lookahead = this._string[this._currentIndex];
                return this._pathSegTypeFromChar(lookahead);
            }

            Source.prototype._pathSegTypeFromChar = function (lookahead) {
                switch (lookahead) {
                    case "Z":
                    case "z":
                        return window.SVGPathSeg.PATHSEG_CLOSEPATH;
                    case "M":
                        return window.SVGPathSeg.PATHSEG_MOVETO_ABS;
                    case "m":
                        return window.SVGPathSeg.PATHSEG_MOVETO_REL;
                    case "L":
                        return window.SVGPathSeg.PATHSEG_LINETO_ABS;
                    case "l":
                        return window.SVGPathSeg.PATHSEG_LINETO_REL;
                    case "C":
                        return window.SVGPathSeg.PATHSEG_CURVETO_CUBIC_ABS;
                    case "c":
                        return window.SVGPathSeg.PATHSEG_CURVETO_CUBIC_REL;
                    case "Q":
                        return window.SVGPathSeg.PATHSEG_CURVETO_QUADRATIC_ABS;
                    case "q":
                        return window.SVGPathSeg.PATHSEG_CURVETO_QUADRATIC_REL;
                    case "A":
                        return window.SVGPathSeg.PATHSEG_ARC_ABS;
                    case "a":
                        return window.SVGPathSeg.PATHSEG_ARC_REL;
                    case "H":
                        return window.SVGPathSeg.PATHSEG_LINETO_HORIZONTAL_ABS;
                    case "h":
                        return window.SVGPathSeg.PATHSEG_LINETO_HORIZONTAL_REL;
                    case "V":
                        return window.SVGPathSeg.PATHSEG_LINETO_VERTICAL_ABS;
                    case "v":
                        return window.SVGPathSeg.PATHSEG_LINETO_VERTICAL_REL;
                    case "S":
                        return window.SVGPathSeg.PATHSEG_CURVETO_CUBIC_SMOOTH_ABS;
                    case "s":
                        return window.SVGPathSeg.PATHSEG_CURVETO_CUBIC_SMOOTH_REL;
                    case "T":
                        return window.SVGPathSeg.PATHSEG_CURVETO_QUADRATIC_SMOOTH_ABS;
                    case "t":
                        return window.SVGPathSeg.PATHSEG_CURVETO_QUADRATIC_SMOOTH_REL;
                    default:
                        return window.SVGPathSeg.PATHSEG_UNKNOWN;
                }
            }

            Source.prototype._nextCommandHelper = function (lookahead, previousCommand) {
                // Check for remaining coordinates in the current command.
                if ((lookahead == "+" || lookahead == "-" || lookahead == "." || (lookahead >= "0" && lookahead <= "9")) && previousCommand != window.SVGPathSeg.PATHSEG_CLOSEPATH) {
                    if (previousCommand == window.SVGPathSeg.PATHSEG_MOVETO_ABS)
                        return window.SVGPathSeg.PATHSEG_LINETO_ABS;
                    if (previousCommand == window.SVGPathSeg.PATHSEG_MOVETO_REL)
                        return window.SVGPathSeg.PATHSEG_LINETO_REL;
                    return previousCommand;
                }
                return window.SVGPathSeg.PATHSEG_UNKNOWN;
            }

            Source.prototype.initialCommandIsMoveTo = function () {
                // If the path is empty it is still valid, so return true.
                if (!this.hasMoreData())
                    return true;
                var command = this.peekSegmentType();
                // Path must start with moveTo.
                return command == window.SVGPathSeg.PATHSEG_MOVETO_ABS || command == window.SVGPathSeg.PATHSEG_MOVETO_REL;
            }

            // Parse a number from an SVG path. This very closely follows genericParseNumber(...) from Source/core/svg/SVGParserUtilities.cpp.
            // Spec: http://www.w3.org/TR/SVG11/single-page.html#paths-PathDataBNF
            Source.prototype._parseNumber = function () {
                var exponent = 0;
                var integer = 0;
                var frac = 1;
                var decimal = 0;
                var sign = 1;
                var expsign = 1;

                var startIndex = this._currentIndex;

                this._skipOptionalSpaces();

                // Read the sign.
                if (this._currentIndex < this._endIndex && this._string.charAt(this._currentIndex) == "+")
                    this._currentIndex++;
                else if (this._currentIndex < this._endIndex && this._string.charAt(this._currentIndex) == "-") {
                    this._currentIndex++;
                    sign = -1;
                }

                if (this._currentIndex == this._endIndex || ((this._string.charAt(this._currentIndex) < "0" || this._string.charAt(this._currentIndex) > "9") && this._string.charAt(this._currentIndex) != "."))
                    // The first character of a number must be one of [0-9+-.].
                    return undefined;

                // Read the integer part, build right-to-left.
                var startIntPartIndex = this._currentIndex;
                while (this._currentIndex < this._endIndex && this._string.charAt(this._currentIndex) >= "0" && this._string.charAt(this._currentIndex) <= "9")
                    this._currentIndex++; // Advance to first non-digit.

                if (this._currentIndex != startIntPartIndex) {
                    var scanIntPartIndex = this._currentIndex - 1;
                    var multiplier = 1;
                    while (scanIntPartIndex >= startIntPartIndex) {
                        integer += multiplier * (this._string.charAt(scanIntPartIndex--) - "0");
                        multiplier *= 10;
                    }
                }

                // Read the decimals.
                if (this._currentIndex < this._endIndex && this._string.charAt(this._currentIndex) == ".") {
                    this._currentIndex++;

                    // There must be a least one digit following the .
                    if (this._currentIndex >= this._endIndex || this._string.charAt(this._currentIndex) < "0" || this._string.charAt(this._currentIndex) > "9")
                        return undefined;
                    while (this._currentIndex < this._endIndex && this._string.charAt(this._currentIndex) >= "0" && this._string.charAt(this._currentIndex) <= "9") {
                        frac *= 10;
                        decimal += (this._string.charAt(this._currentIndex) - "0") / frac;
                        this._currentIndex += 1;
                    }
                }

                // Read the exponent part.
                if (this._currentIndex != startIndex && this._currentIndex + 1 < this._endIndex && (this._string.charAt(this._currentIndex) == "e" || this._string.charAt(this._currentIndex) == "E") && (this._string.charAt(this._currentIndex + 1) != "x" && this._string.charAt(this._currentIndex + 1) != "m")) {
                    this._currentIndex++;

                    // Read the sign of the exponent.
                    if (this._string.charAt(this._currentIndex) == "+") {
                        this._currentIndex++;
                    } else if (this._string.charAt(this._currentIndex) == "-") {
                        this._currentIndex++;
                        expsign = -1;
                    }

                    // There must be an exponent.
                    if (this._currentIndex >= this._endIndex || this._string.charAt(this._currentIndex) < "0" || this._string.charAt(this._currentIndex) > "9")
                        return undefined;

                    while (this._currentIndex < this._endIndex && this._string.charAt(this._currentIndex) >= "0" && this._string.charAt(this._currentIndex) <= "9") {
                        exponent *= 10;
                        exponent += (this._string.charAt(this._currentIndex) - "0");
                        this._currentIndex++;
                    }
                }

                var number = integer + decimal;
                number *= sign;

                if (exponent)
                    number *= Math.pow(10, expsign * exponent);

                if (startIndex == this._currentIndex)
                    return undefined;

                this._skipOptionalSpacesOrDelimiter();

                return number;
            }

            Source.prototype._parseArcFlag = function () {
                if (this._currentIndex >= this._endIndex)
                    return undefined;
                var flag = false;
                var flagChar = this._string.charAt(this._currentIndex++);
                if (flagChar == "0")
                    flag = false;
                else if (flagChar == "1")
                    flag = true;
                else
                    return undefined;

                this._skipOptionalSpacesOrDelimiter();
                return flag;
            }

            Source.prototype.parseSegment = function () {
                var lookahead = this._string[this._currentIndex];
                var command = this._pathSegTypeFromChar(lookahead);
                if (command == window.SVGPathSeg.PATHSEG_UNKNOWN) {
                    // Possibly an implicit command. Not allowed if this is the first command.
                    if (this._previousCommand == window.SVGPathSeg.PATHSEG_UNKNOWN)
                        return null;
                    command = this._nextCommandHelper(lookahead, this._previousCommand);
                    if (command == window.SVGPathSeg.PATHSEG_UNKNOWN)
                        return null;
                } else {
                    this._currentIndex++;
                }

                this._previousCommand = command;

                switch (command) {
                    case window.SVGPathSeg.PATHSEG_MOVETO_REL:
                        return new window.SVGPathSegMovetoRel(owningPathSegList, this._parseNumber(), this._parseNumber());
                    case window.SVGPathSeg.PATHSEG_MOVETO_ABS:
                        return new window.SVGPathSegMovetoAbs(owningPathSegList, this._parseNumber(), this._parseNumber());
                    case window.SVGPathSeg.PATHSEG_LINETO_REL:
                        return new window.SVGPathSegLinetoRel(owningPathSegList, this._parseNumber(), this._parseNumber());
                    case window.SVGPathSeg.PATHSEG_LINETO_ABS:
                        return new window.SVGPathSegLinetoAbs(owningPathSegList, this._parseNumber(), this._parseNumber());
                    case window.SVGPathSeg.PATHSEG_LINETO_HORIZONTAL_REL:
                        return new window.SVGPathSegLinetoHorizontalRel(owningPathSegList, this._parseNumber());
                    case window.SVGPathSeg.PATHSEG_LINETO_HORIZONTAL_ABS:
                        return new window.SVGPathSegLinetoHorizontalAbs(owningPathSegList, this._parseNumber());
                    case window.SVGPathSeg.PATHSEG_LINETO_VERTICAL_REL:
                        return new window.SVGPathSegLinetoVerticalRel(owningPathSegList, this._parseNumber());
                    case window.SVGPathSeg.PATHSEG_LINETO_VERTICAL_ABS:
                        return new window.SVGPathSegLinetoVerticalAbs(owningPathSegList, this._parseNumber());
                    case window.SVGPathSeg.PATHSEG_CLOSEPATH:
                        this._skipOptionalSpaces();
                        return new window.SVGPathSegClosePath(owningPathSegList);
                    case window.SVGPathSeg.PATHSEG_CURVETO_CUBIC_REL:
                        var points = { x1: this._parseNumber(), y1: this._parseNumber(), x2: this._parseNumber(), y2: this._parseNumber(), x: this._parseNumber(), y: this._parseNumber() };
                        return new window.SVGPathSegCurvetoCubicRel(owningPathSegList, points.x, points.y, points.x1, points.y1, points.x2, points.y2);
                    case window.SVGPathSeg.PATHSEG_CURVETO_CUBIC_ABS:
                        var points = { x1: this._parseNumber(), y1: this._parseNumber(), x2: this._parseNumber(), y2: this._parseNumber(), x: this._parseNumber(), y: this._parseNumber() };
                        return new window.SVGPathSegCurvetoCubicAbs(owningPathSegList, points.x, points.y, points.x1, points.y1, points.x2, points.y2);
                    case window.SVGPathSeg.PATHSEG_CURVETO_CUBIC_SMOOTH_REL:
                        var points = { x2: this._parseNumber(), y2: this._parseNumber(), x: this._parseNumber(), y: this._parseNumber() };
                        return new window.SVGPathSegCurvetoCubicSmoothRel(owningPathSegList, points.x, points.y, points.x2, points.y2);
                    case window.SVGPathSeg.PATHSEG_CURVETO_CUBIC_SMOOTH_ABS:
                        var points = { x2: this._parseNumber(), y2: this._parseNumber(), x: this._parseNumber(), y: this._parseNumber() };
                        return new window.SVGPathSegCurvetoCubicSmoothAbs(owningPathSegList, points.x, points.y, points.x2, points.y2);
                    case window.SVGPathSeg.PATHSEG_CURVETO_QUADRATIC_REL:
                        var points = { x1: this._parseNumber(), y1: this._parseNumber(), x: this._parseNumber(), y: this._parseNumber() };
                        return new window.SVGPathSegCurvetoQuadraticRel(owningPathSegList, points.x, points.y, points.x1, points.y1);
                    case window.SVGPathSeg.PATHSEG_CURVETO_QUADRATIC_ABS:
                        var points = { x1: this._parseNumber(), y1: this._parseNumber(), x: this._parseNumber(), y: this._parseNumber() };
                        return new window.SVGPathSegCurvetoQuadraticAbs(owningPathSegList, points.x, points.y, points.x1, points.y1);
                    case window.SVGPathSeg.PATHSEG_CURVETO_QUADRATIC_SMOOTH_REL:
                        return new window.SVGPathSegCurvetoQuadraticSmoothRel(owningPathSegList, this._parseNumber(), this._parseNumber());
                    case window.SVGPathSeg.PATHSEG_CURVETO_QUADRATIC_SMOOTH_ABS:
                        return new window.SVGPathSegCurvetoQuadraticSmoothAbs(owningPathSegList, this._parseNumber(), this._parseNumber());
                    case window.SVGPathSeg.PATHSEG_ARC_REL:
                        var points = { x1: this._parseNumber(), y1: this._parseNumber(), arcAngle: this._parseNumber(), arcLarge: this._parseArcFlag(), arcSweep: this._parseArcFlag(), x: this._parseNumber(), y: this._parseNumber() };
                        return new window.SVGPathSegArcRel(owningPathSegList, points.x, points.y, points.x1, points.y1, points.arcAngle, points.arcLarge, points.arcSweep);
                    case window.SVGPathSeg.PATHSEG_ARC_ABS:
                        var points = { x1: this._parseNumber(), y1: this._parseNumber(), arcAngle: this._parseNumber(), arcLarge: this._parseArcFlag(), arcSweep: this._parseArcFlag(), x: this._parseNumber(), y: this._parseNumber() };
                        return new window.SVGPathSegArcAbs(owningPathSegList, points.x, points.y, points.x1, points.y1, points.arcAngle, points.arcLarge, points.arcSweep);
                    default:
                        throw "Unknown path seg type."
                }
            }

            var builder = new Builder();
            var source = new Source(string);

            if (!source.initialCommandIsMoveTo())
                return [];
            while (source.hasMoreData()) {
                var pathSeg = source.parseSegment();
                if (!pathSeg)
                    return [];
                builder.appendSegment(pathSeg);
            }

            return builder.pathSegList;
        }
    }
}());

! function (e) {


    /*!
 * Author : Matteo Bruni - https://www.matteobruni.it
 * MIT license: https://opensource.org/licenses/MIT
 * Demo / Generator : https://particles.js.org/
 * GitHub : https://www.github.com/matteobruni/tsparticles
 * How to use? : Check the GitHub README
 * v1.33.2
 */
    (function webpackUniversalModuleDefinition(root, factory) {
        if (typeof exports === 'object' && typeof module === 'object')
            module.exports = factory();
        else if (typeof define === 'function' && define.amd)
            define([], factory);
        else {
            var a = factory();
            for (var i in a) (typeof exports === 'object' ? exports : root)[i] = a[i];
        }
    })(this, function () {
        return /******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
/******/
/************************************************************************/
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for (var key in definition) {
/******/ 				if (__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
                            /******/
                        }
                        /******/
                    }
                    /******/
                };
                /******/
            })();
/******/
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
                /******/
            })();
/******/
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if (typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
                        /******/
                    }
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
                    /******/
                };
                /******/
            })();
            /******/
            /************************************************************************/
            var __webpack_exports__ = {};
            // ESM COMPAT FLAG
            __webpack_require__.r(__webpack_exports__);

            // EXPORTS
            __webpack_require__.d(__webpack_exports__, {
                "AbsorberClickMode": () => (/* reexport */ AbsorberClickMode),
                "AlterType": () => (/* reexport */ AlterType),
                "AnimationStatus": () => (/* reexport */ AnimationStatus),
                "CanvasUtils": () => (/* reexport */ CanvasUtils_namespaceObject),
                "Circle": () => (/* reexport */ Circle),
                "CircleWarp": () => (/* reexport */ CircleWarp),
                "ClickMode": () => (/* reexport */ ClickMode),
                "CollisionMode": () => (/* reexport */ CollisionMode),
                "ColorUtils": () => (/* reexport */ ColorUtils_namespaceObject),
                "Constants": () => (/* reexport */ Constants),
                "Container": () => (/* reexport */ Container),
                "DestroyMode": () => (/* reexport */ DestroyMode),
                "DestroyType": () => (/* reexport */ DestroyType),
                "DivMode": () => (/* reexport */ DivMode),
                "DivType": () => (/* reexport */ DivType),
                "EasingType": () => (/* reexport */ EasingType),
                "EmitterClickMode": () => (/* reexport */ EmitterClickMode),
                "ExternalInteractorBase": () => (/* reexport */ ExternalInteractorBase),
                "HoverMode": () => (/* reexport */ HoverMode),
                "InlineArrangement": () => (/* reexport */ InlineArrangement),
                "InteractivityDetect": () => (/* reexport */ InteractivityDetect),
                "InteractorType": () => (/* reexport */ InteractorType),
                "Main": () => (/* reexport */ Main),
                "MoveDirection": () => (/* reexport */ MoveDirection),
                "MoveType": () => (/* reexport */ MoveType),
                "NumberUtils": () => (/* reexport */ NumberUtils_namespaceObject),
                "OrbitType": () => (/* reexport */ OrbitType),
                "OutMode": () => (/* reexport */ OutMode),
                "OutModeDirection": () => (/* reexport */ OutModeDirection),
                "Particle": () => (/* reexport */ Particle),
                "ParticlesInteractorBase": () => (/* reexport */ ParticlesInteractorBase),
                "Point": () => (/* reexport */ Point),
                "Rectangle": () => (/* reexport */ Rectangle),
                "RotateDirection": () => (/* reexport */ RotateDirection),
                "ShapeType": () => (/* reexport */ ShapeType),
                "SizeMode": () => (/* reexport */ SizeMode),
                "StartValueType": () => (/* reexport */ StartValueType),
                "ThemeMode": () => (/* reexport */ ThemeMode),
                "TiltDirection": () => (/* reexport */ TiltDirection),
                "Type": () => (/* reexport */ Type),
                "Utils": () => (/* reexport */ Utils_namespaceObject),
                "Vector": () => (/* reexport */ Vector),
                "loadFull": () => (/* reexport */ loadFull),
                "pJSDom": () => (/* binding */ pJSDom),
                "particlesJS": () => (/* binding */ particlesJS),
                "tsParticles": () => (/* binding */ tsParticles)
            });

            // NAMESPACE OBJECT: ./dist/browser/Utils/NumberUtils.js
            var NumberUtils_namespaceObject = {};
            __webpack_require__.r(NumberUtils_namespaceObject);
            __webpack_require__.d(NumberUtils_namespaceObject, {
                "calcEasing": () => (calcEasing),
                "clamp": () => (clamp),
                "collisionVelocity": () => (collisionVelocity),
                "getDistance": () => (getDistance),
                "getDistances": () => (getDistances),
                "getParticleBaseVelocity": () => (getParticleBaseVelocity),
                "getParticleDirectionAngle": () => (getParticleDirectionAngle),
                "getRangeMax": () => (getRangeMax),
                "getRangeMin": () => (getRangeMin),
                "getRangeValue": () => (getRangeValue),
                "getValue": () => (getValue),
                "mix": () => (mix),
                "randomInRange": () => (randomInRange),
                "setRangeValue": () => (setRangeValue)
            });

            // NAMESPACE OBJECT: ./dist/browser/Utils/Utils.js
            var Utils_namespaceObject = {};
            __webpack_require__.r(Utils_namespaceObject);
            __webpack_require__.d(Utils_namespaceObject, {
                "animate": () => (animate),
                "areBoundsInside": () => (areBoundsInside),
                "arrayRandomIndex": () => (arrayRandomIndex),
                "calculateBounds": () => (calculateBounds),
                "cancelAnimation": () => (cancelAnimation),
                "circleBounce": () => (circleBounce),
                "circleBounceDataFromParticle": () => (circleBounceDataFromParticle),
                "deepExtend": () => (deepExtend),
                "divMode": () => (divMode),
                "divModeExecute": () => (divModeExecute),
                "isDivModeEnabled": () => (isDivModeEnabled),
                "isInArray": () => (isInArray),
                "isPointInside": () => (isPointInside),
                "isSsr": () => (isSsr),
                "itemFromArray": () => (itemFromArray),
                "loadFont": () => (loadFont),
                "rectBounce": () => (rectBounce),
                "singleDivModeExecute": () => (singleDivModeExecute)
            });

            // NAMESPACE OBJECT: ./dist/browser/Utils/ColorUtils.js
            var ColorUtils_namespaceObject = {};
            __webpack_require__.r(ColorUtils_namespaceObject);
            __webpack_require__.d(ColorUtils_namespaceObject, {
                "colorMix": () => (colorMix),
                "colorToHsl": () => (colorToHsl),
                "colorToRgb": () => (colorToRgb),
                "getHslFromAnimation": () => (getHslFromAnimation),
                "getLinkColor": () => (getLinkColor),
                "getLinkRandomColor": () => (getLinkRandomColor),
                "getRandomRgbColor": () => (getRandomRgbColor),
                "getStyleFromHsl": () => (getStyleFromHsl),
                "getStyleFromHsv": () => (getStyleFromHsv),
                "getStyleFromRgb": () => (getStyleFromRgb),
                "hslToHsv": () => (hslToHsv),
                "hslToRgb": () => (hslToRgb),
                "hslaToHsva": () => (hslaToHsva),
                "hslaToRgba": () => (hslaToRgba),
                "hsvToHsl": () => (hsvToHsl),
                "hsvToRgb": () => (hsvToRgb),
                "hsvaToHsla": () => (hsvaToHsla),
                "hsvaToRgba": () => (hsvaToRgba),
                "rgbToHsl": () => (rgbToHsl),
                "rgbToHsv": () => (rgbToHsv),
                "rgbaToHsva": () => (rgbaToHsva),
                "stringToAlpha": () => (stringToAlpha),
                "stringToRgb": () => (stringToRgb)
            });

            // NAMESPACE OBJECT: ./dist/browser/Utils/CanvasUtils.js
            var CanvasUtils_namespaceObject = {};
            __webpack_require__.r(CanvasUtils_namespaceObject);
            __webpack_require__.d(CanvasUtils_namespaceObject, {
                "clear": () => (clear),
                "drawConnectLine": () => (drawConnectLine),
                "drawEllipse": () => (drawEllipse),
                "drawGrabLine": () => (drawGrabLine),
                "drawLinkLine": () => (drawLinkLine),
                "drawLinkTriangle": () => (drawLinkTriangle),
                "drawParticle": () => (drawParticle),
                "drawParticlePlugin": () => (drawParticlePlugin),
                "drawPlugin": () => (drawPlugin),
                "drawShape": () => (drawShape),
                "drawShapeAfterEffect": () => (drawShapeAfterEffect),
                "gradient": () => (gradient),
                "paintBase": () => (paintBase)
            });

            ;// CONCATENATED MODULE: ./dist/browser/pjs.js
            const initPjs = main => {
                const particlesJS = (tagId, options) => {
                    return main.load(tagId, options);
                };

                particlesJS.load = (tagId, pathConfigJson, callback) => {
                    main.loadJSON(tagId, pathConfigJson).then(container => {
                        if (container) {
                            callback(container);
                        }
                    }).catch(() => {
                        callback(undefined);
                    });
                };

                particlesJS.setOnClickHandler = callback => {
                    main.setOnClickHandler(callback);
                };

                const pJSDom = main.dom();
                return {
                    particlesJS,
                    pJSDom
                };
            };


            ;// CONCATENATED MODULE: ./dist/browser/Enums/Directions/MoveDirection.js
            var MoveDirection;

            (function (MoveDirection) {
                MoveDirection["bottom"] = "bottom";
                MoveDirection["bottomLeft"] = "bottom-left";
                MoveDirection["bottomRight"] = "bottom-right";
                MoveDirection["left"] = "left";
                MoveDirection["none"] = "none";
                MoveDirection["right"] = "right";
                MoveDirection["top"] = "top";
                MoveDirection["topLeft"] = "top-left";
                MoveDirection["topRight"] = "top-right";
            })(MoveDirection || (MoveDirection = {}));
            ;// CONCATENATED MODULE: ./dist/browser/Enums/Directions/RotateDirection.js
            var RotateDirection;

            (function (RotateDirection) {
                RotateDirection["clockwise"] = "clockwise";
                RotateDirection["counterClockwise"] = "counter-clockwise";
                RotateDirection["random"] = "random";
            })(RotateDirection || (RotateDirection = {}));
            ;// CONCATENATED MODULE: ./dist/browser/Enums/Directions/OutModeDirection.js
            var OutModeDirection;

            (function (OutModeDirection) {
                OutModeDirection["bottom"] = "bottom";
                OutModeDirection["left"] = "left";
                OutModeDirection["right"] = "right";
                OutModeDirection["top"] = "top";
            })(OutModeDirection || (OutModeDirection = {}));
            ;// CONCATENATED MODULE: ./dist/browser/Enums/Directions/TiltDirection.js
            var TiltDirection;

            (function (TiltDirection) {
                TiltDirection["clockwise"] = "clockwise";
                TiltDirection["counterClockwise"] = "counter-clockwise";
                TiltDirection["random"] = "random";
            })(TiltDirection || (TiltDirection = {}));
            ;// CONCATENATED MODULE: ./dist/browser/Enums/Directions/index.js




            ;// CONCATENATED MODULE: ./dist/browser/Enums/Modes/ClickMode.js
            var ClickMode;

            (function (ClickMode) {
                ClickMode["attract"] = "attract";
                ClickMode["bubble"] = "bubble";
                ClickMode["push"] = "push";
                ClickMode["remove"] = "remove";
                ClickMode["repulse"] = "repulse";
                ClickMode["pause"] = "pause";
                ClickMode["trail"] = "trail";
            })(ClickMode || (ClickMode = {}));
            ;// CONCATENATED MODULE: ./dist/browser/Enums/Modes/DestroyMode.js
            var DestroyMode;

            (function (DestroyMode) {
                DestroyMode["none"] = "none";
                DestroyMode["split"] = "split";
            })(DestroyMode || (DestroyMode = {}));
            ;// CONCATENATED MODULE: ./dist/browser/Enums/Modes/DivMode.js
            var DivMode;

            (function (DivMode) {
                DivMode["bounce"] = "bounce";
                DivMode["bubble"] = "bubble";
                DivMode["repulse"] = "repulse";
            })(DivMode || (DivMode = {}));
            ;// CONCATENATED MODULE: ./dist/browser/Enums/Modes/HoverMode.js
            var HoverMode;

            (function (HoverMode) {
                HoverMode["attract"] = "attract";
                HoverMode["bounce"] = "bounce";
                HoverMode["bubble"] = "bubble";
                HoverMode["connect"] = "connect";
                HoverMode["grab"] = "grab";
                HoverMode["light"] = "light";
                HoverMode["repulse"] = "repulse";
                HoverMode["slow"] = "slow";
                HoverMode["trail"] = "trail";
            })(HoverMode || (HoverMode = {}));
            ;// CONCATENATED MODULE: ./dist/browser/Enums/Modes/CollisionMode.js
            var CollisionMode;

            (function (CollisionMode) {
                CollisionMode["absorb"] = "absorb";
                CollisionMode["bounce"] = "bounce";
                CollisionMode["destroy"] = "destroy";
            })(CollisionMode || (CollisionMode = {}));
            ;// CONCATENATED MODULE: ./dist/browser/Enums/Modes/OutMode.js
            var OutMode;

            (function (OutMode) {
                OutMode["bounce"] = "bounce";
                OutMode["bounceHorizontal"] = "bounce-horizontal";
                OutMode["bounceVertical"] = "bounce-vertical";
                OutMode["none"] = "none";
                OutMode["out"] = "out";
                OutMode["destroy"] = "destroy";
                OutMode["split"] = "split";
            })(OutMode || (OutMode = {}));
            ;// CONCATENATED MODULE: ./dist/browser/Enums/Modes/SizeMode.js
            var SizeMode;

            (function (SizeMode) {
                SizeMode["precise"] = "precise";
                SizeMode["percent"] = "percent";
            })(SizeMode || (SizeMode = {}));
            ;// CONCATENATED MODULE: ./dist/browser/Enums/Modes/ThemeMode.js
            var ThemeMode;

            (function (ThemeMode) {
                ThemeMode["any"] = "any";
                ThemeMode["dark"] = "dark";
                ThemeMode["light"] = "light";
            })(ThemeMode || (ThemeMode = {}));
            ;// CONCATENATED MODULE: ./dist/browser/Enums/Modes/index.js








            ;// CONCATENATED MODULE: ./dist/browser/Enums/AnimationStatus.js
            var AnimationStatus;

            (function (AnimationStatus) {
                AnimationStatus[AnimationStatus["increasing"] = 0] = "increasing";
                AnimationStatus[AnimationStatus["decreasing"] = 1] = "decreasing";
            })(AnimationStatus || (AnimationStatus = {}));
            ;// CONCATENATED MODULE: ./dist/browser/Enums/Types/AlterType.js
            var AlterType;

            (function (AlterType) {
                AlterType["darken"] = "darken";
                AlterType["enlighten"] = "enlighten";
            })(AlterType || (AlterType = {}));
            ;// CONCATENATED MODULE: ./dist/browser/Enums/Types/DestroyType.js
            var DestroyType;

            (function (DestroyType) {
                DestroyType["none"] = "none";
                DestroyType["max"] = "max";
                DestroyType["min"] = "min";
            })(DestroyType || (DestroyType = {}));
            ;// CONCATENATED MODULE: ./dist/browser/Enums/Types/InteractorType.js
            var InteractorType;

            (function (InteractorType) {
                InteractorType[InteractorType["External"] = 0] = "External";
                InteractorType[InteractorType["Particles"] = 1] = "Particles";
            })(InteractorType || (InteractorType = {}));
            ;// CONCATENATED MODULE: ./dist/browser/Enums/Types/ShapeType.js
            var ShapeType;

            (function (ShapeType) {
                ShapeType["char"] = "char";
                ShapeType["character"] = "character";
                ShapeType["circle"] = "circle";
                ShapeType["edge"] = "edge";
                ShapeType["image"] = "image";
                ShapeType["images"] = "images";
                ShapeType["line"] = "line";
                ShapeType["polygon"] = "polygon";
                ShapeType["square"] = "square";
                ShapeType["star"] = "star";
                ShapeType["triangle"] = "triangle";
            })(ShapeType || (ShapeType = {}));
            ;// CONCATENATED MODULE: ./dist/browser/Enums/Types/StartValueType.js
            var StartValueType;

            (function (StartValueType) {
                StartValueType["max"] = "max";
                StartValueType["min"] = "min";
                StartValueType["random"] = "random";
            })(StartValueType || (StartValueType = {}));
            ;// CONCATENATED MODULE: ./dist/browser/Enums/Types/DivType.js
            var DivType;

            (function (DivType) {
                DivType["circle"] = "circle";
                DivType["rectangle"] = "rectangle";
            })(DivType || (DivType = {}));
            ;// CONCATENATED MODULE: ./dist/browser/Enums/Types/EasingType.js
            var EasingType;

            (function (EasingType) {
                EasingType["easeOutBack"] = "ease-out-back";
                EasingType["easeOutCirc"] = "ease-out-circ";
                EasingType["easeOutCubic"] = "ease-out-cubic";
                EasingType["easeOutQuad"] = "ease-out-quad";
                EasingType["easeOutQuart"] = "ease-out-quart";
                EasingType["easeOutQuint"] = "ease-out-quint";
                EasingType["easeOutExpo"] = "ease-out-expo";
                EasingType["easeOutSine"] = "ease-out-sine";
            })(EasingType || (EasingType = {}));
            ;// CONCATENATED MODULE: ./dist/browser/Enums/Types/OrbitType.js
            var OrbitType;

            (function (OrbitType) {
                OrbitType["front"] = "front";
                OrbitType["back"] = "back";
            })(OrbitType || (OrbitType = {}));
            ;// CONCATENATED MODULE: ./dist/browser/Enums/Types/index.js








            ;// CONCATENATED MODULE: ./dist/browser/Enums/InteractivityDetect.js
            var InteractivityDetect;

            (function (InteractivityDetect) {
                InteractivityDetect["canvas"] = "canvas";
                InteractivityDetect["parent"] = "parent";
                InteractivityDetect["window"] = "window";
            })(InteractivityDetect || (InteractivityDetect = {}));
            ;// CONCATENATED MODULE: ./dist/browser/Enums/index.js





            ;// CONCATENATED MODULE: ./dist/browser/Core/Particle/Vector.js
            class Vector {
                constructor(x, y) {
                    let defX, defY;

                    if (y === undefined) {
                        if (typeof x === "number") {
                            throw new Error("tsParticles - Vector not initialized correctly");
                        }

                        const coords = x;
                        [defX, defY] = [coords.x, coords.y];
                    } else {
                        [defX, defY] = [x, y];
                    }

                    this.x = defX;
                    this.y = defY;
                }

                static clone(source) {
                    return Vector.create(source.x, source.y);
                }

                static create(x, y) {
                    return new Vector(x, y);
                }

                static get origin() {
                    return Vector.create(0, 0);
                }

                get angle() {
                    return Math.atan2(this.y, this.x);
                }

                set angle(angle) {
                    this.updateFromAngle(angle, this.length);
                }

                get length() {
                    return Math.sqrt(Math.pow(this.x, 2) + Math.pow(this.y, 2));
                }

                set length(length) {
                    this.updateFromAngle(this.angle, length);
                }

                add(v) {
                    return Vector.create(this.x + v.x, this.y + v.y);
                }

                addTo(v) {
                    this.x += v.x;
                    this.y += v.y;
                }

                sub(v) {
                    return Vector.create(this.x - v.x, this.y - v.y);
                }

                subFrom(v) {
                    this.x -= v.x;
                    this.y -= v.y;
                }

                mult(n) {
                    return Vector.create(this.x * n, this.y * n);
                }

                multTo(n) {
                    this.x *= n;
                    this.y *= n;
                }

                div(n) {
                    return Vector.create(this.x / n, this.y / n);
                }

                divTo(n) {
                    this.x /= n;
                    this.y /= n;
                }

                distanceTo(v) {
                    return this.sub(v).length;
                }

                getLengthSq() {
                    return Math.pow(this.x, 2) + Math.pow(this.y, 2);
                }

                distanceToSq(v) {
                    return this.sub(v).getLengthSq();
                }

                manhattanDistanceTo(v) {
                    return Math.abs(v.x - this.x) + Math.abs(v.y - this.y);
                }

                copy() {
                    return Vector.clone(this);
                }

                setTo(velocity) {
                    this.x = velocity.x;
                    this.y = velocity.y;
                }

                rotate(angle) {
                    return Vector.create(this.x * Math.cos(angle) - this.y * Math.sin(angle), this.x * Math.sin(angle) + this.y * Math.cos(angle));
                }

                updateFromAngle(angle, length) {
                    this.x = Math.cos(angle) * length;
                    this.y = Math.sin(angle) * length;
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Utils/NumberUtils.js


            function clamp(num, min, max) {
                return Math.min(Math.max(num, min), max);
            }
            function mix(comp1, comp2, weight1, weight2) {
                return Math.floor((comp1 * weight1 + comp2 * weight2) / (weight1 + weight2));
            }
            function randomInRange(r) {
                const max = getRangeMax(r);
                let min = getRangeMin(r);

                if (max === min) {
                    min = 0;
                }

                return Math.random() * (max - min) + min;
            }
            function getRangeValue(value) {
                return typeof value === "number" ? value : randomInRange(value);
            }
            function getRangeMin(value) {
                return typeof value === "number" ? value : value.min;
            }
            function getRangeMax(value) {
                return typeof value === "number" ? value : value.max;
            }
            function setRangeValue(source, value) {
                if (source === value || value === undefined && typeof source === "number") {
                    return source;
                }

                const min = getRangeMin(source),
                    max = getRangeMax(source);
                return value !== undefined ? {
                    min: Math.min(min, value),
                    max: Math.max(max, value)
                } : setRangeValue(min, max);
            }
            function getValue(options) {
                const random = options.random;
                const {
                    enable,
                    minimumValue
                } = typeof random === "boolean" ? {
                    enable: random,
                    minimumValue: 0
                } : random;
                return enable ? getRangeValue(setRangeValue(options.value, minimumValue)) : getRangeValue(options.value);
            }
            function getDistances(pointA, pointB) {
                const dx = pointA.x - pointB.x;
                const dy = pointA.y - pointB.y;
                return {
                    dx: dx,
                    dy: dy,
                    distance: Math.sqrt(dx * dx + dy * dy)
                };
            }
            function getDistance(pointA, pointB) {
                return getDistances(pointA, pointB).distance;
            }
            function getParticleDirectionAngle(direction) {
                if (typeof direction === "number") {
                    return direction * Math.PI / 180;
                } else {
                    switch (direction) {
                        case MoveDirection.top:
                            return -Math.PI / 2;

                        case MoveDirection.topRight:
                            return -Math.PI / 4;

                        case MoveDirection.right:
                            return 0;

                        case MoveDirection.bottomRight:
                            return Math.PI / 4;

                        case MoveDirection.bottom:
                            return Math.PI / 2;

                        case MoveDirection.bottomLeft:
                            return 3 * Math.PI / 4;

                        case MoveDirection.left:
                            return Math.PI;

                        case MoveDirection.topLeft:
                            return -3 * Math.PI / 4;

                        case MoveDirection.none:
                        default:
                            return Math.random() * Math.PI * 2;
                    }
                }
            }
            function getParticleBaseVelocity(direction) {
                const baseVelocity = Vector.origin;
                baseVelocity.length = 1;
                baseVelocity.angle = direction;
                return baseVelocity;
            }
            function collisionVelocity(v1, v2, m1, m2) {
                return Vector.create(v1.x * (m1 - m2) / (m1 + m2) + v2.x * 2 * m2 / (m1 + m2), v1.y);
            }
            function calcEasing(value, type) {
                switch (type) {
                    case EasingType.easeOutQuad:
                        return 1 - Math.pow(1 - value, 2);

                    case EasingType.easeOutCubic:
                        return 1 - Math.pow(1 - value, 3);

                    case EasingType.easeOutQuart:
                        return 1 - Math.pow(1 - value, 4);

                    case EasingType.easeOutQuint:
                        return 1 - Math.pow(1 - value, 5);

                    case EasingType.easeOutExpo:
                        return value === 1 ? 1 : 1 - Math.pow(2, -10 * value);

                    case EasingType.easeOutSine:
                        return Math.sin(value * Math.PI / 2);

                    case EasingType.easeOutBack:
                        {
                            const c1 = 1.70158;
                            const c3 = c1 + 1;
                            return 1 + c3 * Math.pow(value - 1, 3) + c1 * Math.pow(value - 1, 2);
                        }

                    case EasingType.easeOutCirc:
                        return Math.sqrt(1 - Math.pow(value - 1, 2));

                    default:
                        return value;
                }
            }
            ;// CONCATENATED MODULE: ./dist/browser/Utils/Utils.js
            var __awaiter = undefined && undefined.__awaiter || function (thisArg, _arguments, P, generator) {
                function adopt(value) {
                    return value instanceof P ? value : new P(function (resolve) {
                        resolve(value);
                    });
                }

                return new (P || (P = Promise))(function (resolve, reject) {
                    function fulfilled(value) {
                        try {
                            step(generator.next(value));
                        } catch (e) {
                            reject(e);
                        }
                    }

                    function rejected(value) {
                        try {
                            step(generator["throw"](value));
                        } catch (e) {
                            reject(e);
                        }
                    }

                    function step(result) {
                        result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected);
                    }

                    step((generator = generator.apply(thisArg, _arguments || [])).next());
                });
            };





            function rectSideBounce(pSide, pOtherSide, rectSide, rectOtherSide, velocity, factor) {
                const res = {
                    bounced: false
                };

                if (pOtherSide.min >= rectOtherSide.min && pOtherSide.min <= rectOtherSide.max && pOtherSide.max >= rectOtherSide.min && pOtherSide.max <= rectOtherSide.max) {
                    if (pSide.max >= rectSide.min && pSide.max <= (rectSide.max + rectSide.min) / 2 && velocity > 0 || pSide.min <= rectSide.max && pSide.min > (rectSide.max + rectSide.min) / 2 && velocity < 0) {
                        res.velocity = velocity * -factor;
                        res.bounced = true;
                    }
                }

                return res;
            }

            function checkSelector(element, selectors) {
                if (selectors instanceof Array) {
                    for (const selector of selectors) {
                        if (element.matches(selector)) {
                            return true;
                        }
                    }

                    return false;
                } else {
                    return element.matches(selectors);
                }
            }

            function isSsr() {
                return typeof window === "undefined" || !window;
            }
            function animate() {
                return isSsr() ? callback => setTimeout(callback) : callback => (window.requestAnimationFrame || window.webkitRequestAnimationFrame || window.mozRequestAnimationFrame || window.oRequestAnimationFrame || window.msRequestAnimationFrame || window.setTimeout)(callback);
            }
            function cancelAnimation() {
                return isSsr() ? handle => clearTimeout(handle) : handle => (window.cancelAnimationFrame || window.webkitCancelRequestAnimationFrame || window.mozCancelRequestAnimationFrame || window.oCancelRequestAnimationFrame || window.msCancelRequestAnimationFrame || window.clearTimeout)(handle);
            }
            function isInArray(value, array) {
                return value === array || array instanceof Array && array.indexOf(value) > -1;
            }
            function loadFont(character) {
                var _a, _b;

                return __awaiter(this, void 0, void 0, function* () {
                    try {
                        yield document.fonts.load(`${(_a = character.weight) !== null && _a !== void 0 ? _a : "400"} 36px '${(_b = character.font) !== null && _b !== void 0 ? _b : "Verdana"}'`);
                    } catch (_c) { }
                });
            }
            function arrayRandomIndex(array) {
                return Math.floor(Math.random() * array.length);
            }
            function itemFromArray(array, index, useIndex = true) {
                const fixedIndex = index !== undefined && useIndex ? index % array.length : arrayRandomIndex(array);
                return array[fixedIndex];
            }
            function isPointInside(point, size, radius, direction) {
                return areBoundsInside(calculateBounds(point, radius !== null && radius !== void 0 ? radius : 0), size, direction);
            }
            function areBoundsInside(bounds, size, direction) {
                let inside = true;

                if (!direction || direction === OutModeDirection.bottom) {
                    inside = bounds.top < size.height;
                }

                if (inside && (!direction || direction === OutModeDirection.left)) {
                    inside = bounds.right > 0;
                }

                if (inside && (!direction || direction === OutModeDirection.right)) {
                    inside = bounds.left < size.width;
                }

                if (inside && (!direction || direction === OutModeDirection.top)) {
                    inside = bounds.bottom > 0;
                }

                return inside;
            }
            function calculateBounds(point, radius) {
                return {
                    bottom: point.y + radius,
                    left: point.x - radius,
                    right: point.x + radius,
                    top: point.y - radius
                };
            }
            function deepExtend(destination, ...sources) {
                for (const source of sources) {
                    if (source === undefined || source === null) {
                        continue;
                    }

                    if (typeof source !== "object") {
                        destination = source;
                        continue;
                    }

                    const sourceIsArray = Array.isArray(source);

                    if (sourceIsArray && (typeof destination !== "object" || !destination || !Array.isArray(destination))) {
                        destination = [];
                    } else if (!sourceIsArray && (typeof destination !== "object" || !destination || Array.isArray(destination))) {
                        destination = {};
                    }

                    for (const key in source) {
                        if (key === "__proto__") {
                            continue;
                        }

                        const sourceDict = source;
                        const value = sourceDict[key];
                        const isObject = typeof value === "object";
                        const destDict = destination;
                        destDict[key] = isObject && Array.isArray(value) ? value.map(v => deepExtend(destDict[key], v)) : deepExtend(destDict[key], value);
                    }
                }

                return destination;
            }
            function isDivModeEnabled(mode, divs) {
                return divs instanceof Array ? !!divs.find(t => t.enable && isInArray(mode, t.mode)) : isInArray(mode, divs.mode);
            }
            function divModeExecute(mode, divs, callback) {
                if (divs instanceof Array) {
                    for (const div of divs) {
                        const divMode = div.mode;
                        const divEnabled = div.enable;

                        if (divEnabled && isInArray(mode, divMode)) {
                            singleDivModeExecute(div, callback);
                        }
                    }
                } else {
                    const divMode = divs.mode;
                    const divEnabled = divs.enable;

                    if (divEnabled && isInArray(mode, divMode)) {
                        singleDivModeExecute(divs, callback);
                    }
                }
            }
            function singleDivModeExecute(div, callback) {
                const selectors = div.selectors;

                if (selectors instanceof Array) {
                    for (const selector of selectors) {
                        callback(selector, div);
                    }
                } else {
                    callback(selectors, div);
                }
            }
            function divMode(divs, element) {
                if (!element || !divs) {
                    return;
                }

                if (divs instanceof Array) {
                    return divs.find(d => checkSelector(element, d.selectors));
                } else if (checkSelector(element, divs.selectors)) {
                    return divs;
                }
            }
            function circleBounceDataFromParticle(p) {
                return {
                    position: p.getPosition(),
                    radius: p.getRadius(),
                    mass: p.getMass(),
                    velocity: p.velocity,
                    factor: Vector.create(getValue(p.options.bounce.horizontal), getValue(p.options.bounce.vertical))
                };
            }
            function circleBounce(p1, p2) {
                const xVelocityDiff = p1.velocity.x;
                const yVelocityDiff = p1.velocity.y;
                const pos1 = p1.position;
                const pos2 = p2.position;
                const xDist = pos2.x - pos1.x;
                const yDist = pos2.y - pos1.y;

                if (xVelocityDiff * xDist + yVelocityDiff * yDist >= 0) {
                    const angle = -Math.atan2(pos2.y - pos1.y, pos2.x - pos1.x);
                    const m1 = p1.mass;
                    const m2 = p2.mass;
                    const u1 = p1.velocity.rotate(angle);
                    const u2 = p2.velocity.rotate(angle);
                    const v1 = collisionVelocity(u1, u2, m1, m2);
                    const v2 = collisionVelocity(u2, u1, m1, m2);
                    const vFinal1 = v1.rotate(-angle);
                    const vFinal2 = v2.rotate(-angle);
                    p1.velocity.x = vFinal1.x * p1.factor.x;
                    p1.velocity.y = vFinal1.y * p1.factor.y;
                    p2.velocity.x = vFinal2.x * p2.factor.x;
                    p2.velocity.y = vFinal2.y * p2.factor.y;
                }
            }
            function rectBounce(particle, divBounds) {
                const pPos = particle.getPosition();
                const size = particle.getRadius();
                const bounds = calculateBounds(pPos, size);
                const resH = rectSideBounce({
                    min: bounds.left,
                    max: bounds.right
                }, {
                    min: bounds.top,
                    max: bounds.bottom
                }, {
                    min: divBounds.left,
                    max: divBounds.right
                }, {
                    min: divBounds.top,
                    max: divBounds.bottom
                }, particle.velocity.x, getValue(particle.options.bounce.horizontal));

                if (resH.bounced) {
                    if (resH.velocity !== undefined) {
                        particle.velocity.x = resH.velocity;
                    }

                    if (resH.position !== undefined) {
                        particle.position.x = resH.position;
                    }
                }

                const resV = rectSideBounce({
                    min: bounds.top,
                    max: bounds.bottom
                }, {
                    min: bounds.left,
                    max: bounds.right
                }, {
                    min: divBounds.top,
                    max: divBounds.bottom
                }, {
                    min: divBounds.left,
                    max: divBounds.right
                }, particle.velocity.y, getValue(particle.options.bounce.vertical));

                if (resV.bounced) {
                    if (resV.velocity !== undefined) {
                        particle.velocity.y = resV.velocity;
                    }

                    if (resV.position !== undefined) {
                        particle.position.y = resV.position;
                    }
                }
            }
            ;// CONCATENATED MODULE: ./dist/browser/Utils/Constants.js
            class Constants { }
            Constants.canvasClass = "tsparticles-canvas-el";
            Constants.randomColorValue = "random";
            Constants.midColorValue = "mid";
            Constants.touchEndEvent = "touchend";
            Constants.mouseDownEvent = "mousedown";
            Constants.mouseUpEvent = "mouseup";
            Constants.mouseMoveEvent = "mousemove";
            Constants.touchStartEvent = "touchstart";
            Constants.touchMoveEvent = "touchmove";
            Constants.mouseLeaveEvent = "mouseleave";
            Constants.mouseOutEvent = "mouseout";
            Constants.touchCancelEvent = "touchcancel";
            Constants.resizeEvent = "resize";
            Constants.visibilityChangeEvent = "visibilitychange";
            Constants.noPolygonDataLoaded = "No polygon data loaded.";
            Constants.noPolygonFound = "No polygon found, you need to specify SVG url in config.";
            ;// CONCATENATED MODULE: ./dist/browser/Utils/ColorUtils.js




            function hue2rgb(p, q, t) {
                let tCalc = t;

                if (tCalc < 0) {
                    tCalc += 1;
                }

                if (tCalc > 1) {
                    tCalc -= 1;
                }

                if (tCalc < 1 / 6) {
                    return p + (q - p) * 6 * tCalc;
                }

                if (tCalc < 1 / 2) {
                    return q;
                }

                if (tCalc < 2 / 3) {
                    return p + (q - p) * (2 / 3 - tCalc) * 6;
                }

                return p;
            }

            function stringToRgba(input) {
                if (input.startsWith("rgb")) {
                    const regex = /rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*(,\s*([\d.]+)\s*)?\)/i;
                    const result = regex.exec(input);
                    return result ? {
                        a: result.length > 4 ? parseFloat(result[5]) : 1,
                        b: parseInt(result[3], 10),
                        g: parseInt(result[2], 10),
                        r: parseInt(result[1], 10)
                    } : undefined;
                } else if (input.startsWith("hsl")) {
                    const regex = /hsla?\(\s*(\d+)\s*,\s*(\d+)%\s*,\s*(\d+)%\s*(,\s*([\d.]+)\s*)?\)/i;
                    const result = regex.exec(input);
                    return result ? hslaToRgba({
                        a: result.length > 4 ? parseFloat(result[5]) : 1,
                        h: parseInt(result[1], 10),
                        l: parseInt(result[3], 10),
                        s: parseInt(result[2], 10)
                    }) : undefined;
                } else if (input.startsWith("hsv")) {
                    const regex = /hsva?\(\s*(\d+)°\s*,\s*(\d+)%\s*,\s*(\d+)%\s*(,\s*([\d.]+)\s*)?\)/i;
                    const result = regex.exec(input);
                    return result ? hsvaToRgba({
                        a: result.length > 4 ? parseFloat(result[5]) : 1,
                        h: parseInt(result[1], 10),
                        s: parseInt(result[2], 10),
                        v: parseInt(result[3], 10)
                    }) : undefined;
                } else {
                    const shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])([a-f\d])?$/i;
                    const hexFixed = input.replace(shorthandRegex, (_m, r, g, b, a) => {
                        return r + r + g + g + b + b + (a !== undefined ? a + a : "");
                    });
                    const regex = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})?$/i;
                    const result = regex.exec(hexFixed);
                    return result ? {
                        a: result[4] !== undefined ? parseInt(result[4], 16) / 0xff : 1,
                        b: parseInt(result[3], 16),
                        g: parseInt(result[2], 16),
                        r: parseInt(result[1], 16)
                    } : undefined;
                }
            }

            function colorToRgb(input, index, useIndex = true) {
                var _a, _b, _c;

                if (input === undefined) {
                    return;
                }

                const color = typeof input === "string" ? {
                    value: input
                } : input;
                let res;

                if (typeof color.value === "string") {
                    if (color.value === Constants.randomColorValue) {
                        res = getRandomRgbColor();
                    } else {
                        res = stringToRgb(color.value);
                    }
                } else {
                    if (color.value instanceof Array) {
                        const colorSelected = itemFromArray(color.value, index, useIndex);
                        res = colorToRgb({
                            value: colorSelected
                        });
                    } else {
                        const colorValue = color.value;
                        const rgbColor = (_a = colorValue.rgb) !== null && _a !== void 0 ? _a : color.value;

                        if (rgbColor.r !== undefined) {
                            res = rgbColor;
                        } else {
                            const hslColor = (_b = colorValue.hsl) !== null && _b !== void 0 ? _b : color.value;

                            if (hslColor.h !== undefined && hslColor.l !== undefined) {
                                res = hslToRgb(hslColor);
                            } else {
                                const hsvColor = (_c = colorValue.hsv) !== null && _c !== void 0 ? _c : color.value;

                                if (hsvColor.h !== undefined && hsvColor.v !== undefined) {
                                    res = hsvToRgb(hsvColor);
                                }
                            }
                        }
                    }
                }

                return res;
            }
            function colorToHsl(color, index, useIndex = true) {
                const rgb = colorToRgb(color, index, useIndex);
                return rgb !== undefined ? rgbToHsl(rgb) : undefined;
            }
            function rgbToHsl(color) {
                const r1 = color.r / 255;
                const g1 = color.g / 255;
                const b1 = color.b / 255;
                const max = Math.max(r1, g1, b1);
                const min = Math.min(r1, g1, b1);
                const res = {
                    h: 0,
                    l: (max + min) / 2,
                    s: 0
                };

                if (max != min) {
                    res.s = res.l < 0.5 ? (max - min) / (max + min) : (max - min) / (2.0 - max - min);
                    res.h = r1 === max ? (g1 - b1) / (max - min) : res.h = g1 === max ? 2.0 + (b1 - r1) / (max - min) : 4.0 + (r1 - g1) / (max - min);
                }

                res.l *= 100;
                res.s *= 100;
                res.h *= 60;

                if (res.h < 0) {
                    res.h += 360;
                }

                return res;
            }
            function stringToAlpha(input) {
                var _a;

                return (_a = stringToRgba(input)) === null || _a === void 0 ? void 0 : _a.a;
            }
            function stringToRgb(input) {
                return stringToRgba(input);
            }
            function hslToRgb(hsl) {
                const result = {
                    b: 0,
                    g: 0,
                    r: 0
                };
                const hslPercent = {
                    h: hsl.h / 360,
                    l: hsl.l / 100,
                    s: hsl.s / 100
                };

                if (hslPercent.s === 0) {
                    result.b = hslPercent.l;
                    result.g = hslPercent.l;
                    result.r = hslPercent.l;
                } else {
                    const q = hslPercent.l < 0.5 ? hslPercent.l * (1 + hslPercent.s) : hslPercent.l + hslPercent.s - hslPercent.l * hslPercent.s;
                    const p = 2 * hslPercent.l - q;
                    result.r = hue2rgb(p, q, hslPercent.h + 1 / 3);
                    result.g = hue2rgb(p, q, hslPercent.h);
                    result.b = hue2rgb(p, q, hslPercent.h - 1 / 3);
                }

                result.r = Math.floor(result.r * 255);
                result.g = Math.floor(result.g * 255);
                result.b = Math.floor(result.b * 255);
                return result;
            }
            function hslaToRgba(hsla) {
                const rgbResult = hslToRgb(hsla);
                return {
                    a: hsla.a,
                    b: rgbResult.b,
                    g: rgbResult.g,
                    r: rgbResult.r
                };
            }
            function hslToHsv(hsl) {
                const l = hsl.l / 100,
                    sl = hsl.s / 100;
                const v = l + sl * Math.min(l, 1 - l),
                    sv = !v ? 0 : 2 * (1 - l / v);
                return {
                    h: hsl.h,
                    s: sv * 100,
                    v: v * 100
                };
            }
            function hslaToHsva(hsla) {
                const hsvResult = hslToHsv(hsla);
                return {
                    a: hsla.a,
                    h: hsvResult.h,
                    s: hsvResult.s,
                    v: hsvResult.v
                };
            }
            function hsvToHsl(hsv) {
                const v = hsv.v / 100,
                    sv = hsv.s / 100;
                const l = v * (1 - sv / 2),
                    sl = l === 0 || l === 1 ? 0 : (v - l) / Math.min(l, 1 - l);
                return {
                    h: hsv.h,
                    l: l * 100,
                    s: sl * 100
                };
            }
            function hsvaToHsla(hsva) {
                const hslResult = hsvToHsl(hsva);
                return {
                    a: hsva.a,
                    h: hslResult.h,
                    l: hslResult.l,
                    s: hslResult.s
                };
            }
            function hsvToRgb(hsv) {
                const result = {
                    b: 0,
                    g: 0,
                    r: 0
                };
                const hsvPercent = {
                    h: hsv.h / 60,
                    s: hsv.s / 100,
                    v: hsv.v / 100
                };
                const c = hsvPercent.v * hsvPercent.s,
                    x = c * (1 - Math.abs(hsvPercent.h % 2 - 1));
                let tempRgb;

                if (hsvPercent.h >= 0 && hsvPercent.h <= 1) {
                    tempRgb = {
                        r: c,
                        g: x,
                        b: 0
                    };
                } else if (hsvPercent.h > 1 && hsvPercent.h <= 2) {
                    tempRgb = {
                        r: x,
                        g: c,
                        b: 0
                    };
                } else if (hsvPercent.h > 2 && hsvPercent.h <= 3) {
                    tempRgb = {
                        r: 0,
                        g: c,
                        b: x
                    };
                } else if (hsvPercent.h > 3 && hsvPercent.h <= 4) {
                    tempRgb = {
                        r: 0,
                        g: x,
                        b: c
                    };
                } else if (hsvPercent.h > 4 && hsvPercent.h <= 5) {
                    tempRgb = {
                        r: x,
                        g: 0,
                        b: c
                    };
                } else if (hsvPercent.h > 5 && hsvPercent.h <= 6) {
                    tempRgb = {
                        r: c,
                        g: 0,
                        b: x
                    };
                }

                if (tempRgb) {
                    const m = hsvPercent.v - c;
                    result.r = Math.floor((tempRgb.r + m) * 255);
                    result.g = Math.floor((tempRgb.g + m) * 255);
                    result.b = Math.floor((tempRgb.b + m) * 255);
                }

                return result;
            }
            function hsvaToRgba(hsva) {
                const rgbResult = hsvToRgb(hsva);
                return {
                    a: hsva.a,
                    b: rgbResult.b,
                    g: rgbResult.g,
                    r: rgbResult.r
                };
            }
            function rgbToHsv(rgb) {
                const rgbPercent = {
                    r: rgb.r / 255,
                    g: rgb.g / 255,
                    b: rgb.b / 255
                },
                    xMax = Math.max(rgbPercent.r, rgbPercent.g, rgbPercent.b),
                    xMin = Math.min(rgbPercent.r, rgbPercent.g, rgbPercent.b),
                    v = xMax,
                    c = xMax - xMin;
                let h = 0;

                if (v === rgbPercent.r) {
                    h = 60 * ((rgbPercent.g - rgbPercent.b) / c);
                } else if (v === rgbPercent.g) {
                    h = 60 * (2 + (rgbPercent.b - rgbPercent.r) / c);
                } else if (v === rgbPercent.b) {
                    h = 60 * (4 + (rgbPercent.r - rgbPercent.g) / c);
                }

                const s = !v ? 0 : c / v;
                return {
                    h,
                    s: s * 100,
                    v: v * 100
                };
            }
            function rgbaToHsva(rgba) {
                const hsvResult = rgbToHsv(rgba);
                return {
                    a: rgba.a,
                    h: hsvResult.h,
                    s: hsvResult.s,
                    v: hsvResult.v
                };
            }
            function getRandomRgbColor(min) {
                const fixedMin = min !== null && min !== void 0 ? min : 0;
                return {
                    b: Math.floor(randomInRange(setRangeValue(fixedMin, 256))),
                    g: Math.floor(randomInRange(setRangeValue(fixedMin, 256))),
                    r: Math.floor(randomInRange(setRangeValue(fixedMin, 256)))
                };
            }
            function getStyleFromRgb(color, opacity) {
                return `rgba(${color.r}, ${color.g}, ${color.b}, ${opacity !== null && opacity !== void 0 ? opacity : 1})`;
            }
            function getStyleFromHsl(color, opacity) {
                return `hsla(${color.h}, ${color.s}%, ${color.l}%, ${opacity !== null && opacity !== void 0 ? opacity : 1})`;
            }
            function getStyleFromHsv(color, opacity) {
                return getStyleFromHsl(hsvToHsl(color), opacity);
            }
            function colorMix(color1, color2, size1, size2) {
                let rgb1 = color1;
                let rgb2 = color2;

                if (rgb1.r === undefined) {
                    rgb1 = hslToRgb(color1);
                }

                if (rgb2.r === undefined) {
                    rgb2 = hslToRgb(color2);
                }

                return {
                    b: mix(rgb1.b, rgb2.b, size1, size2),
                    g: mix(rgb1.g, rgb2.g, size1, size2),
                    r: mix(rgb1.r, rgb2.r, size1, size2)
                };
            }
            function getLinkColor(p1, p2, linkColor) {
                var _a, _b;

                if (linkColor === Constants.randomColorValue) {
                    return getRandomRgbColor();
                } else if (linkColor === "mid") {
                    const sourceColor = (_a = p1.getFillColor()) !== null && _a !== void 0 ? _a : p1.getStrokeColor();
                    const destColor = (_b = p2 === null || p2 === void 0 ? void 0 : p2.getFillColor()) !== null && _b !== void 0 ? _b : p2 === null || p2 === void 0 ? void 0 : p2.getStrokeColor();

                    if (sourceColor && destColor && p2) {
                        return colorMix(sourceColor, destColor, p1.getRadius(), p2.getRadius());
                    } else {
                        const hslColor = sourceColor !== null && sourceColor !== void 0 ? sourceColor : destColor;

                        if (hslColor) {
                            return hslToRgb(hslColor);
                        }
                    }
                } else {
                    return linkColor;
                }
            }
            function getLinkRandomColor(optColor, blink, consent) {
                const color = typeof optColor === "string" ? optColor : optColor.value;

                if (color === Constants.randomColorValue) {
                    if (consent) {
                        return colorToRgb({
                            value: color
                        });
                    } else if (blink) {
                        return Constants.randomColorValue;
                    } else {
                        return Constants.midColorValue;
                    }
                } else {
                    return colorToRgb({
                        value: color
                    });
                }
            }
            function getHslFromAnimation(animation) {
                return animation !== undefined ? {
                    h: animation.h.value,
                    s: animation.s.value,
                    l: animation.l.value
                } : undefined;
            }
            ;// CONCATENATED MODULE: ./dist/browser/Utils/CanvasUtils.js



            function drawLine(context, begin, end) {
                context.beginPath();
                context.moveTo(begin.x, begin.y);
                context.lineTo(end.x, end.y);
                context.closePath();
            }

            function drawTriangle(context, p1, p2, p3) {
                context.beginPath();
                context.moveTo(p1.x, p1.y);
                context.lineTo(p2.x, p2.y);
                context.lineTo(p3.x, p3.y);
                context.closePath();
            }

            function paintBase(context, dimension, baseColor) {
                context.save();
                context.fillStyle = baseColor !== null && baseColor !== void 0 ? baseColor : "rgba(0,0,0,0)";
                context.fillRect(0, 0, dimension.width, dimension.height);
                context.restore();
            }
            function clear(context, dimension) {
                context.clearRect(0, 0, dimension.width, dimension.height);
            }
            function drawLinkLine(context, width, begin, end, maxDistance, canvasSize, warp, backgroundMask, composite, colorLine, opacity, shadow) {
                let drawn = false;

                if (getDistance(begin, end) <= maxDistance) {
                    drawLine(context, begin, end);
                    drawn = true;
                } else if (warp) {
                    let pi1;
                    let pi2;
                    const endNE = {
                        x: end.x - canvasSize.width,
                        y: end.y
                    };
                    const d1 = getDistances(begin, endNE);

                    if (d1.distance <= maxDistance) {
                        const yi = begin.y - d1.dy / d1.dx * begin.x;
                        pi1 = {
                            x: 0,
                            y: yi
                        };
                        pi2 = {
                            x: canvasSize.width,
                            y: yi
                        };
                    } else {
                        const endSW = {
                            x: end.x,
                            y: end.y - canvasSize.height
                        };
                        const d2 = getDistances(begin, endSW);

                        if (d2.distance <= maxDistance) {
                            const yi = begin.y - d2.dy / d2.dx * begin.x;
                            const xi = -yi / (d2.dy / d2.dx);
                            pi1 = {
                                x: xi,
                                y: 0
                            };
                            pi2 = {
                                x: xi,
                                y: canvasSize.height
                            };
                        } else {
                            const endSE = {
                                x: end.x - canvasSize.width,
                                y: end.y - canvasSize.height
                            };
                            const d3 = getDistances(begin, endSE);

                            if (d3.distance <= maxDistance) {
                                const yi = begin.y - d3.dy / d3.dx * begin.x;
                                const xi = -yi / (d3.dy / d3.dx);
                                pi1 = {
                                    x: xi,
                                    y: yi
                                };
                                pi2 = {
                                    x: pi1.x + canvasSize.width,
                                    y: pi1.y + canvasSize.height
                                };
                            }
                        }
                    }

                    if (pi1 && pi2) {
                        drawLine(context, begin, pi1);
                        drawLine(context, end, pi2);
                        drawn = true;
                    }
                }

                if (!drawn) {
                    return;
                }

                context.lineWidth = width;

                if (backgroundMask) {
                    context.globalCompositeOperation = composite;
                }

                context.strokeStyle = getStyleFromRgb(colorLine, opacity);

                if (shadow.enable) {
                    const shadowColor = colorToRgb(shadow.color);

                    if (shadowColor) {
                        context.shadowBlur = shadow.blur;
                        context.shadowColor = getStyleFromRgb(shadowColor);
                    }
                }

                context.stroke();
            }
            function drawLinkTriangle(context, pos1, pos2, pos3, backgroundMask, composite, colorTriangle, opacityTriangle) {
                drawTriangle(context, pos1, pos2, pos3);

                if (backgroundMask) {
                    context.globalCompositeOperation = composite;
                }

                context.fillStyle = getStyleFromRgb(colorTriangle, opacityTriangle);
                context.fill();
            }
            function drawConnectLine(context, width, lineStyle, begin, end) {
                context.save();
                drawLine(context, begin, end);
                context.lineWidth = width;
                context.strokeStyle = lineStyle;
                context.stroke();
                context.restore();
            }
            function gradient(context, p1, p2, opacity) {
                const gradStop = Math.floor(p2.getRadius() / p1.getRadius());
                const color1 = p1.getFillColor();
                const color2 = p2.getFillColor();

                if (!color1 || !color2) {
                    return;
                }

                const sourcePos = p1.getPosition();
                const destPos = p2.getPosition();
                const midRgb = colorMix(color1, color2, p1.getRadius(), p2.getRadius());
                const grad = context.createLinearGradient(sourcePos.x, sourcePos.y, destPos.x, destPos.y);
                grad.addColorStop(0, getStyleFromHsl(color1, opacity));
                grad.addColorStop(gradStop > 1 ? 1 : gradStop, getStyleFromRgb(midRgb, opacity));
                grad.addColorStop(1, getStyleFromHsl(color2, opacity));
                return grad;
            }
            function drawGrabLine(context, width, begin, end, colorLine, opacity) {
                context.save();
                drawLine(context, begin, end);
                context.strokeStyle = getStyleFromRgb(colorLine, opacity);
                context.lineWidth = width;
                context.stroke();
                context.restore();
            }
            function drawParticle(container, context, particle, delta, fillColorValue, strokeColorValue, backgroundMask, composite, radius, opacity, shadow) {
                const pos = particle.getPosition();
                const tiltOptions = particle.options.tilt;
                const rollOptions = particle.options.roll;
                context.save();

                if (tiltOptions.enable || rollOptions.enable) {
                    context.setTransform(rollOptions.enable ? Math.cos(particle.rollAngle) : 1, tiltOptions.enable ? Math.cos(particle.tilt.value) * particle.tilt.cosDirection : 0, tiltOptions.enable ? Math.sin(particle.tilt.value) * particle.tilt.sinDirection : 0, rollOptions.enable ? Math.sin(particle.rollAngle) : 1, pos.x, pos.y);
                } else {
                    context.translate(pos.x, pos.y);
                }

                context.beginPath();
                const angle = particle.rotate.value + (particle.options.rotate.path ? particle.velocity.angle : 0);

                if (angle !== 0) {
                    context.rotate(angle);
                }

                if (backgroundMask) {
                    context.globalCompositeOperation = composite;
                }

                const shadowColor = particle.shadowColor;

                if (shadow.enable && shadowColor) {
                    context.shadowBlur = shadow.blur;
                    context.shadowColor = getStyleFromRgb(shadowColor);
                    context.shadowOffsetX = shadow.offset.x;
                    context.shadowOffsetY = shadow.offset.y;
                }

                if (fillColorValue) {
                    context.fillStyle = fillColorValue;
                }

                const stroke = particle.stroke;
                context.lineWidth = particle.strokeWidth;

                if (strokeColorValue) {
                    context.strokeStyle = strokeColorValue;
                }

                drawShape(container, context, particle, radius, opacity, delta);

                if (stroke.width > 0) {
                    context.stroke();
                }

                if (particle.close) {
                    context.closePath();
                }

                if (particle.fill) {
                    context.fill();
                }

                context.restore();
                context.save();

                if (tiltOptions.enable) {
                    context.setTransform(1, Math.cos(particle.tilt.value) * particle.tilt.cosDirection, Math.sin(particle.tilt.value) * particle.tilt.sinDirection, 1, pos.x, pos.y);
                } else {
                    context.translate(pos.x, pos.y);
                }

                if (angle !== 0) {
                    context.rotate(angle);
                }

                if (backgroundMask) {
                    context.globalCompositeOperation = composite;
                }

                drawShapeAfterEffect(container, context, particle, radius, opacity, delta);
                context.restore();
            }
            function drawShape(container, context, particle, radius, opacity, delta) {
                if (!particle.shape) {
                    return;
                }

                const drawer = container.drawers.get(particle.shape);

                if (!drawer) {
                    return;
                }

                drawer.draw(context, particle, radius, opacity, delta, container.retina.pixelRatio);
            }
            function drawShapeAfterEffect(container, context, particle, radius, opacity, delta) {
                if (!particle.shape) {
                    return;
                }

                const drawer = container.drawers.get(particle.shape);

                if (!(drawer === null || drawer === void 0 ? void 0 : drawer.afterEffect)) {
                    return;
                }

                drawer.afterEffect(context, particle, radius, opacity, delta, container.retina.pixelRatio);
            }
            function drawPlugin(context, plugin, delta) {
                if (!plugin.draw) {
                    return;
                }

                context.save();
                plugin.draw(context, delta);
                context.restore();
            }
            function drawParticlePlugin(context, plugin, particle, delta) {
                if (plugin.drawParticle !== undefined) {
                    context.save();
                    plugin.drawParticle(context, particle, delta);
                    context.restore();
                }
            }
            function drawEllipse(context, particle, fillColorValue, radius, opacity, width, rotation, start, end) {
                const pos = particle.getPosition();

                if (fillColorValue) {
                    context.strokeStyle = getStyleFromHsl(fillColorValue, opacity);
                }

                if (width === 0) {
                    return;
                }

                context.lineWidth = width;
                const rotationRadian = rotation * Math.PI / 180;
                context.beginPath();
                context.ellipse(pos.x, pos.y, radius / 2, radius * 2, rotationRadian, start, end);
                context.stroke();
            }
            ;// CONCATENATED MODULE: ./dist/browser/Utils/Range.js
            class Range {
                constructor(x, y) {
                    this.position = {
                        x: x,
                        y: y
                    };
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Utils/Circle.js

            class Circle extends Range {
                constructor(x, y, radius) {
                    super(x, y);
                    this.radius = radius;
                }

                contains(point) {
                    const d = Math.pow(point.x - this.position.x, 2) + Math.pow(point.y - this.position.y, 2);
                    return d <= this.radius * this.radius;
                }

                intersects(range) {
                    const rect = range;
                    const circle = range;
                    const pos1 = this.position;
                    const pos2 = range.position;
                    const xDist = Math.abs(pos2.x - pos1.x);
                    const yDist = Math.abs(pos2.y - pos1.y);
                    const r = this.radius;

                    if (circle.radius !== undefined) {
                        const rSum = r + circle.radius;
                        const dist = Math.sqrt(xDist * xDist + yDist + yDist);
                        return rSum > dist;
                    } else if (rect.size !== undefined) {
                        const w = rect.size.width;
                        const h = rect.size.height;
                        const edges = Math.pow(xDist - w, 2) + Math.pow(yDist - h, 2);

                        if (xDist > r + w || yDist > r + h) {
                            return false;
                        }

                        if (xDist <= w || yDist <= h) {
                            return true;
                        }

                        return edges <= r * r;
                    }

                    return false;
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Utils/Rectangle.js

            class Rectangle extends Range {
                constructor(x, y, width, height) {
                    super(x, y);
                    this.size = {
                        height: height,
                        width: width
                    };
                }

                contains(point) {
                    const w = this.size.width;
                    const h = this.size.height;
                    const pos = this.position;
                    return point.x >= pos.x && point.x <= pos.x + w && point.y >= pos.y && point.y <= pos.y + h;
                }

                intersects(range) {
                    const rect = range;
                    const circle = range;
                    const w = this.size.width;
                    const h = this.size.height;
                    const pos1 = this.position;
                    const pos2 = range.position;

                    if (circle.radius !== undefined) {
                        return circle.intersects(this);
                    } else if (rect.size !== undefined) {
                        const size2 = rect.size;
                        const w2 = size2.width;
                        const h2 = size2.height;
                        return pos2.x < pos1.x + w && pos2.x + w2 > pos1.x && pos2.y < pos1.y + h && pos2.y + h2 > pos1.y;
                    }

                    return false;
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Utils/CircleWarp.js


            class CircleWarp extends Circle {
                constructor(x, y, radius, canvasSize) {
                    super(x, y, radius);
                    this.canvasSize = canvasSize;
                    this.canvasSize = {
                        height: canvasSize.height,
                        width: canvasSize.width
                    };
                }

                contains(point) {
                    if (super.contains(point)) {
                        return true;
                    }

                    const posNE = {
                        x: point.x - this.canvasSize.width,
                        y: point.y
                    };

                    if (super.contains(posNE)) {
                        return true;
                    }

                    const posSE = {
                        x: point.x - this.canvasSize.width,
                        y: point.y - this.canvasSize.height
                    };

                    if (super.contains(posSE)) {
                        return true;
                    }

                    const posSW = {
                        x: point.x,
                        y: point.y - this.canvasSize.height
                    };
                    return super.contains(posSW);
                }

                intersects(range) {
                    if (super.intersects(range)) {
                        return true;
                    }

                    const rect = range;
                    const circle = range;
                    const newPos = {
                        x: range.position.x - this.canvasSize.width,
                        y: range.position.y - this.canvasSize.height
                    };

                    if (circle.radius !== undefined) {
                        const biggerCircle = new Circle(newPos.x, newPos.y, circle.radius * 2);
                        return super.intersects(biggerCircle);
                    } else if (rect.size !== undefined) {
                        const rectSW = new Rectangle(newPos.x, newPos.y, rect.size.width * 2, rect.size.height * 2);
                        return super.intersects(rectSW);
                    }

                    return false;
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Utils/EventListeners.js




            function manageListener(element, event, handler, add, options) {
                if (add) {
                    let addOptions = {
                        passive: true
                    };

                    if (typeof options === "boolean") {
                        addOptions.capture = options;
                    } else if (options !== undefined) {
                        addOptions = options;
                    }

                    element.addEventListener(event, handler, addOptions);
                } else {
                    const removeOptions = options;
                    element.removeEventListener(event, handler, removeOptions);
                }
            }

            class EventListeners {
                constructor(container) {
                    this.container = container;
                    this.canPush = true;

                    this.mouseMoveHandler = e => this.mouseTouchMove(e);

                    this.touchStartHandler = e => this.mouseTouchMove(e);

                    this.touchMoveHandler = e => this.mouseTouchMove(e);

                    this.touchEndHandler = () => this.mouseTouchFinish();

                    this.mouseLeaveHandler = () => this.mouseTouchFinish();

                    this.touchCancelHandler = () => this.mouseTouchFinish();

                    this.touchEndClickHandler = e => this.mouseTouchClick(e);

                    this.mouseUpHandler = e => this.mouseTouchClick(e);

                    this.mouseDownHandler = () => this.mouseDown();

                    this.visibilityChangeHandler = () => this.handleVisibilityChange();

                    this.resizeHandler = () => this.handleWindowResize();
                }

                addListeners() {
                    this.manageListeners(true);
                }

                removeListeners() {
                    this.manageListeners(false);
                }

                manageListeners(add) {
                    var _a;

                    const container = this.container;
                    const options = container.actualOptions;
                    const detectType = options.interactivity.detectsOn;
                    let mouseLeaveEvent = Constants.mouseLeaveEvent;

                    if (detectType === InteractivityDetect.window) {
                        container.interactivity.element = window;
                        mouseLeaveEvent = Constants.mouseOutEvent;
                    } else if (detectType === InteractivityDetect.parent && container.canvas.element) {
                        const canvasEl = container.canvas.element;
                        container.interactivity.element = (_a = canvasEl.parentElement) !== null && _a !== void 0 ? _a : canvasEl.parentNode;
                    } else {
                        container.interactivity.element = container.canvas.element;
                    }

                    const interactivityEl = container.interactivity.element;

                    if (!interactivityEl) {
                        return;
                    }

                    const html = interactivityEl;

                    if (options.interactivity.events.onHover.enable || options.interactivity.events.onClick.enable) {
                        manageListener(interactivityEl, Constants.mouseMoveEvent, this.mouseMoveHandler, add);
                        manageListener(interactivityEl, Constants.touchStartEvent, this.touchStartHandler, add);
                        manageListener(interactivityEl, Constants.touchMoveEvent, this.touchMoveHandler, add);

                        if (!options.interactivity.events.onClick.enable) {
                            manageListener(interactivityEl, Constants.touchEndEvent, this.touchEndHandler, add);
                        } else {
                            manageListener(interactivityEl, Constants.touchEndEvent, this.touchEndClickHandler, add);
                            manageListener(interactivityEl, Constants.mouseUpEvent, this.mouseUpHandler, add);
                            manageListener(interactivityEl, Constants.mouseDownEvent, this.mouseDownHandler, add);
                        }

                        manageListener(interactivityEl, mouseLeaveEvent, this.mouseLeaveHandler, add);
                        manageListener(interactivityEl, Constants.touchCancelEvent, this.touchCancelHandler, add);
                    }

                    if (container.canvas.element) {
                        container.canvas.element.style.pointerEvents = html === container.canvas.element ? "initial" : "none";
                    }

                    if (options.interactivity.events.resize) {
                        if (typeof ResizeObserver !== "undefined") {
                            if (this.resizeObserver && !add) {
                                if (container.canvas.element) {
                                    this.resizeObserver.unobserve(container.canvas.element);
                                }

                                this.resizeObserver.disconnect();
                                delete this.resizeObserver;
                            } else if (!this.resizeObserver && add && container.canvas.element) {
                                this.resizeObserver = new ResizeObserver(entries => {
                                    const entry = entries.find(e => e.target === container.canvas.element);

                                    if (!entry) {
                                        return;
                                    }

                                    this.handleWindowResize();
                                });
                                this.resizeObserver.observe(container.canvas.element);
                            }
                        } else {
                            manageListener(window, Constants.resizeEvent, this.resizeHandler, add);
                        }
                    }

                    if (document) {
                        manageListener(document, Constants.visibilityChangeEvent, this.visibilityChangeHandler, add, false);
                    }
                }

                handleWindowResize() {
                    if (this.resizeTimeout) {
                        clearTimeout(this.resizeTimeout);
                        delete this.resizeTimeout;
                    }

                    this.resizeTimeout = setTimeout(() => {
                        var _a;

                        return (_a = this.container.canvas) === null || _a === void 0 ? void 0 : _a.windowResize();
                    }, 500);
                }

                handleVisibilityChange() {
                    const container = this.container;
                    const options = container.actualOptions;
                    this.mouseTouchFinish();

                    if (!options.pauseOnBlur) {
                        return;
                    }

                    if (document === null || document === void 0 ? void 0 : document.hidden) {
                        container.pageHidden = true;
                        container.pause();
                    } else {
                        container.pageHidden = false;

                        if (container.getAnimationStatus()) {
                            container.play(true);
                        } else {
                            container.draw(true);
                        }
                    }
                }

                mouseDown() {
                    const interactivity = this.container.interactivity;

                    if (interactivity) {
                        const mouse = interactivity.mouse;
                        mouse.clicking = true;
                        mouse.downPosition = mouse.position;
                    }
                }

                mouseTouchMove(e) {
                    var _a, _b, _c, _d, _e, _f, _g;

                    const container = this.container;
                    const options = container.actualOptions;

                    if (((_a = container.interactivity) === null || _a === void 0 ? void 0 : _a.element) === undefined) {
                        return;
                    }

                    container.interactivity.mouse.inside = true;
                    let pos;
                    const canvas = container.canvas.element;

                    if (e.type.startsWith("mouse")) {
                        this.canPush = true;
                        const mouseEvent = e;

                        if (container.interactivity.element === window) {
                            if (canvas) {
                                const clientRect = canvas.getBoundingClientRect();
                                pos = {
                                    x: mouseEvent.clientX - clientRect.left,
                                    y: mouseEvent.clientY - clientRect.top
                                };
                            }
                        } else if (options.interactivity.detectsOn === InteractivityDetect.parent) {
                            const source = mouseEvent.target;
                            const target = mouseEvent.currentTarget;
                            const canvasEl = container.canvas.element;

                            if (source && target && canvasEl) {
                                const sourceRect = source.getBoundingClientRect();
                                const targetRect = target.getBoundingClientRect();
                                const canvasRect = canvasEl.getBoundingClientRect();
                                pos = {
                                    x: mouseEvent.offsetX + 2 * sourceRect.left - (targetRect.left + canvasRect.left),
                                    y: mouseEvent.offsetY + 2 * sourceRect.top - (targetRect.top + canvasRect.top)
                                };
                            } else {
                                pos = {
                                    x: (_b = mouseEvent.offsetX) !== null && _b !== void 0 ? _b : mouseEvent.clientX,
                                    y: (_c = mouseEvent.offsetY) !== null && _c !== void 0 ? _c : mouseEvent.clientY
                                };
                            }
                        } else {
                            if (mouseEvent.target === container.canvas.element) {
                                pos = {
                                    x: (_d = mouseEvent.offsetX) !== null && _d !== void 0 ? _d : mouseEvent.clientX,
                                    y: (_e = mouseEvent.offsetY) !== null && _e !== void 0 ? _e : mouseEvent.clientY
                                };
                            }
                        }
                    } else {
                        this.canPush = e.type !== "touchmove";
                        const touchEvent = e;
                        const lastTouch = touchEvent.touches[touchEvent.touches.length - 1];
                        const canvasRect = canvas === null || canvas === void 0 ? void 0 : canvas.getBoundingClientRect();
                        pos = {
                            x: lastTouch.clientX - ((_f = canvasRect === null || canvasRect === void 0 ? void 0 : canvasRect.left) !== null && _f !== void 0 ? _f : 0),
                            y: lastTouch.clientY - ((_g = canvasRect === null || canvasRect === void 0 ? void 0 : canvasRect.top) !== null && _g !== void 0 ? _g : 0)
                        };
                    }

                    const pxRatio = container.retina.pixelRatio;

                    if (pos) {
                        pos.x *= pxRatio;
                        pos.y *= pxRatio;
                    }

                    container.interactivity.mouse.position = pos;
                    container.interactivity.status = Constants.mouseMoveEvent;
                }

                mouseTouchFinish() {
                    const interactivity = this.container.interactivity;

                    if (interactivity === undefined) {
                        return;
                    }

                    const mouse = interactivity.mouse;
                    delete mouse.position;
                    delete mouse.clickPosition;
                    delete mouse.downPosition;
                    interactivity.status = Constants.mouseLeaveEvent;
                    mouse.inside = false;
                    mouse.clicking = false;
                }

                mouseTouchClick(e) {
                    const container = this.container;
                    const options = container.actualOptions;
                    const mouse = container.interactivity.mouse;
                    mouse.inside = true;
                    let handled = false;
                    const mousePosition = mouse.position;

                    if (mousePosition === undefined || !options.interactivity.events.onClick.enable) {
                        return;
                    }

                    for (const [, plugin] of container.plugins) {
                        if (plugin.clickPositionValid !== undefined) {
                            handled = plugin.clickPositionValid(mousePosition);

                            if (handled) {
                                break;
                            }
                        }
                    }

                    if (!handled) {
                        this.doMouseTouchClick(e);
                    }

                    mouse.clicking = false;
                }

                doMouseTouchClick(e) {
                    const container = this.container;
                    const options = container.actualOptions;

                    if (this.canPush) {
                        const mousePos = container.interactivity.mouse.position;

                        if (mousePos) {
                            container.interactivity.mouse.clickPosition = {
                                x: mousePos.x,
                                y: mousePos.y
                            };
                        } else {
                            return;
                        }

                        container.interactivity.mouse.clickTime = new Date().getTime();
                        const onClick = options.interactivity.events.onClick;

                        if (onClick.mode instanceof Array) {
                            for (const mode of onClick.mode) {
                                this.handleClickMode(mode);
                            }
                        } else {
                            this.handleClickMode(onClick.mode);
                        }
                    }

                    if (e.type === "touchend") {
                        setTimeout(() => this.mouseTouchFinish(), 500);
                    }
                }

                handleClickMode(mode) {
                    const container = this.container;
                    const options = container.actualOptions;
                    const pushNb = options.interactivity.modes.push.quantity;
                    const removeNb = options.interactivity.modes.remove.quantity;

                    switch (mode) {
                        case ClickMode.push:
                            {
                                if (pushNb > 0) {
                                    const pushOptions = options.interactivity.modes.push;
                                    const group = itemFromArray([undefined, ...pushOptions.groups]);
                                    const groupOptions = group !== undefined ? container.actualOptions.particles.groups[group] : undefined;
                                    container.particles.push(pushNb, container.interactivity.mouse, groupOptions, group);
                                }

                                break;
                            }

                        case ClickMode.remove:
                            container.particles.removeQuantity(removeNb);
                            break;

                        case ClickMode.bubble:
                            container.bubble.clicking = true;
                            break;

                        case ClickMode.repulse:
                            container.repulse.clicking = true;
                            container.repulse.count = 0;

                            for (const particle of container.repulse.particles) {
                                particle.velocity.setTo(particle.initialVelocity);
                            }

                            container.repulse.particles = [];
                            container.repulse.finish = false;
                            setTimeout(() => {
                                if (!container.destroyed) {
                                    container.repulse.clicking = false;
                                }
                            }, options.interactivity.modes.repulse.duration * 1000);
                            break;

                        case ClickMode.attract:
                            container.attract.clicking = true;
                            container.attract.count = 0;

                            for (const particle of container.attract.particles) {
                                particle.velocity.setTo(particle.initialVelocity);
                            }

                            container.attract.particles = [];
                            container.attract.finish = false;
                            setTimeout(() => {
                                if (!container.destroyed) {
                                    container.attract.clicking = false;
                                }
                            }, options.interactivity.modes.attract.duration * 1000);
                            break;

                        case ClickMode.pause:
                            if (container.getAnimationStatus()) {
                                container.pause();
                            } else {
                                container.play();
                            }

                            break;
                    }

                    for (const [, plugin] of container.plugins) {
                        if (plugin.handleClickMode) {
                            plugin.handleClickMode(mode);
                        }
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Utils/Plugins.js
            const plugins = [];
            const interactorsInitializers = new Map();
            const updatersInitializers = new Map();
            const interactors = new Map();
            const updaters = new Map();
            const presets = new Map();
            const drawers = new Map();
            const pathGenerators = new Map();
            class Plugins {
                static getPlugin(plugin) {
                    return plugins.find(t => t.id === plugin);
                }

                static addPlugin(plugin) {
                    if (!Plugins.getPlugin(plugin.id)) {
                        plugins.push(plugin);
                    }
                }

                static getAvailablePlugins(container) {
                    const res = new Map();

                    for (const plugin of plugins) {
                        if (!plugin.needsPlugin(container.actualOptions)) {
                            continue;
                        }

                        res.set(plugin.id, plugin.getPlugin(container));
                    }

                    return res;
                }

                static loadOptions(options, sourceOptions) {
                    for (const plugin of plugins) {
                        plugin.loadOptions(options, sourceOptions);
                    }
                }

                static getPreset(preset) {
                    return presets.get(preset);
                }

                static addPreset(presetKey, options, override = false) {
                    if (override || !Plugins.getPreset(presetKey)) {
                        presets.set(presetKey, options);
                    }
                }

                static addShapeDrawer(type, drawer) {
                    if (!Plugins.getShapeDrawer(type)) {
                        drawers.set(type, drawer);
                    }
                }

                static getShapeDrawer(type) {
                    return drawers.get(type);
                }

                static getSupportedShapes() {
                    return drawers.keys();
                }

                static getPathGenerator(type) {
                    return pathGenerators.get(type);
                }

                static addPathGenerator(type, pathGenerator) {
                    if (!Plugins.getPathGenerator(type)) {
                        pathGenerators.set(type, pathGenerator);
                    }
                }

                static getInteractors(container) {
                    let res = interactors.get(container);

                    if (!res) {
                        res = [...interactorsInitializers.values()].map(t => t(container));
                        interactors.set(container, res);
                    }

                    return res;
                }

                static addInteractor(name, initInteractor) {
                    interactorsInitializers.set(name, initInteractor);
                }

                static getUpdaters(container) {
                    let res = updaters.get(container);

                    if (!res) {
                        res = [...updatersInitializers.values()].map(t => t(container));
                        updaters.set(container, res);
                    }

                    return res;
                }

                static addParticleUpdater(name, initUpdater) {
                    updatersInitializers.set(name, initUpdater);
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Utils/Point.js
            class Point {
                constructor(position, particle) {
                    this.position = position;
                    this.particle = particle;
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Utils/QuadTree.js



            class QuadTree {
                constructor(rectangle, capacity) {
                    this.rectangle = rectangle;
                    this.capacity = capacity;
                    this.points = [];
                    this.divided = false;
                }

                subdivide() {
                    const x = this.rectangle.position.x;
                    const y = this.rectangle.position.y;
                    const w = this.rectangle.size.width;
                    const h = this.rectangle.size.height;
                    const capacity = this.capacity;
                    this.northEast = new QuadTree(new Rectangle(x, y, w / 2, h / 2), capacity);
                    this.northWest = new QuadTree(new Rectangle(x + w / 2, y, w / 2, h / 2), capacity);
                    this.southEast = new QuadTree(new Rectangle(x, y + h / 2, w / 2, h / 2), capacity);
                    this.southWest = new QuadTree(new Rectangle(x + w / 2, y + h / 2, w / 2, h / 2), capacity);
                    this.divided = true;
                }

                insert(point) {
                    var _a, _b, _c, _d, _e;

                    if (!this.rectangle.contains(point.position)) {
                        return false;
                    }

                    if (this.points.length < this.capacity) {
                        this.points.push(point);
                        return true;
                    }

                    if (!this.divided) {
                        this.subdivide();
                    }

                    return (_e = ((_a = this.northEast) === null || _a === void 0 ? void 0 : _a.insert(point)) || ((_b = this.northWest) === null || _b === void 0 ? void 0 : _b.insert(point)) || ((_c = this.southEast) === null || _c === void 0 ? void 0 : _c.insert(point)) || ((_d = this.southWest) === null || _d === void 0 ? void 0 : _d.insert(point))) !== null && _e !== void 0 ? _e : false;
                }

                queryCircle(position, radius) {
                    return this.query(new Circle(position.x, position.y, radius));
                }

                queryCircleWarp(position, radius, containerOrSize) {
                    const container = containerOrSize;
                    const size = containerOrSize;
                    return this.query(new CircleWarp(position.x, position.y, radius, container.canvas !== undefined ? container.canvas.size : size));
                }

                queryRectangle(position, size) {
                    return this.query(new Rectangle(position.x, position.y, size.width, size.height));
                }

                query(range, found) {
                    var _a, _b, _c, _d;

                    const res = found !== null && found !== void 0 ? found : [];

                    if (!range.intersects(this.rectangle)) {
                        return [];
                    } else {
                        for (const p of this.points) {
                            if (!range.contains(p.position)) {
                                continue;
                            }

                            res.push(p.particle);
                        }

                        if (this.divided) {
                            (_a = this.northEast) === null || _a === void 0 ? void 0 : _a.query(range, res);
                            (_b = this.northWest) === null || _b === void 0 ? void 0 : _b.query(range, res);
                            (_c = this.southEast) === null || _c === void 0 ? void 0 : _c.query(range, res);
                            (_d = this.southWest) === null || _d === void 0 ? void 0 : _d.query(range, res);
                        }
                    }

                    return res;
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Utils/index.js













            ;// CONCATENATED MODULE: ./dist/browser/Core/Canvas.js



            class Canvas {
                constructor(container) {
                    this.container = container;
                    this.size = {
                        height: 0,
                        width: 0
                    };
                    this.context = null;
                    this.generatedCanvas = false;
                }

                init() {
                    this.resize();
                    this.initStyle();
                    this.initCover();
                    this.initTrail();
                    this.initBackground();
                    this.paint();
                }

                loadCanvas(canvas, generatedCanvas) {
                    var _a;

                    if (!canvas.className) {
                        canvas.className = Constants.canvasClass;
                    }

                    if (this.generatedCanvas) {
                        (_a = this.element) === null || _a === void 0 ? void 0 : _a.remove();
                    }

                    this.generatedCanvas = generatedCanvas !== null && generatedCanvas !== void 0 ? generatedCanvas : this.generatedCanvas;
                    this.element = canvas;
                    this.originalStyle = deepExtend({}, this.element.style);
                    this.size.height = canvas.offsetHeight;
                    this.size.width = canvas.offsetWidth;
                    this.context = this.element.getContext("2d");
                    this.container.retina.init();
                    this.initBackground();
                }

                destroy() {
                    var _a;

                    if (this.generatedCanvas) {
                        (_a = this.element) === null || _a === void 0 ? void 0 : _a.remove();
                    }

                    this.draw(ctx => {
                        clear(ctx, this.size);
                    });
                }

                paint() {
                    const options = this.container.actualOptions;
                    this.draw(ctx => {
                        if (options.backgroundMask.enable && options.backgroundMask.cover && this.coverColor) {
                            clear(ctx, this.size);
                            this.paintBase(getStyleFromRgb(this.coverColor, this.coverColor.a));
                        } else {
                            this.paintBase();
                        }
                    });
                }

                clear() {
                    const options = this.container.actualOptions;
                    const trail = options.particles.move.trail;

                    if (options.backgroundMask.enable) {
                        this.paint();
                    } else if (trail.enable && trail.length > 0 && this.trailFillColor) {
                        this.paintBase(getStyleFromRgb(this.trailFillColor, 1 / trail.length));
                    } else {
                        this.draw(ctx => {
                            clear(ctx, this.size);
                        });
                    }
                }

                windowResize() {
                    if (!this.element) {
                        return;
                    }

                    const container = this.container;
                    this.resize();
                    container.actualOptions.setResponsive(this.size.width, container.retina.pixelRatio, container.options);
                    container.particles.setDensity();

                    for (const [, plugin] of container.plugins) {
                        if (plugin.resize !== undefined) {
                            plugin.resize();
                        }
                    }
                }

                resize() {
                    if (!this.element) {
                        return;
                    }

                    const container = this.container;
                    const pxRatio = container.retina.pixelRatio;
                    const size = container.canvas.size;
                    const oldSize = {
                        width: size.width,
                        height: size.height
                    };
                    size.width = this.element.offsetWidth * pxRatio;
                    size.height = this.element.offsetHeight * pxRatio;
                    this.element.width = size.width;
                    this.element.height = size.height;

                    if (this.container.started) {
                        this.resizeFactor = {
                            width: size.width / oldSize.width,
                            height: size.height / oldSize.height
                        };
                    }
                }

                drawConnectLine(p1, p2) {
                    this.draw(ctx => {
                        var _a;

                        const lineStyle = this.lineStyle(p1, p2);

                        if (!lineStyle) {
                            return;
                        }

                        const pos1 = p1.getPosition();
                        const pos2 = p2.getPosition();
                        drawConnectLine(ctx, (_a = p1.linksWidth) !== null && _a !== void 0 ? _a : this.container.retina.linksWidth, lineStyle, pos1, pos2);
                    });
                }

                drawGrabLine(particle, lineColor, opacity, mousePos) {
                    const container = this.container;
                    this.draw(ctx => {
                        var _a;

                        const beginPos = particle.getPosition();
                        drawGrabLine(ctx, (_a = particle.linksWidth) !== null && _a !== void 0 ? _a : container.retina.linksWidth, beginPos, mousePos, lineColor, opacity);
                    });
                }

                drawParticle(particle, delta) {
                    var _a, _b, _c;

                    if (particle.spawning || particle.destroyed) {
                        return;
                    }

                    const pfColor = particle.getFillColor();
                    const psColor = (_a = particle.getStrokeColor()) !== null && _a !== void 0 ? _a : pfColor;

                    if (!pfColor && !psColor) {
                        return;
                    }

                    let [fColor, sColor] = this.getPluginParticleColors(particle);
                    const pOptions = particle.options;
                    const twinkle = pOptions.twinkle.particles;
                    const twinkling = twinkle.enable && Math.random() < twinkle.frequency;

                    if (!fColor || !sColor) {
                        const twinkleRgb = colorToHsl(twinkle.color);

                        if (!fColor) {
                            fColor = twinkling && twinkleRgb !== undefined ? twinkleRgb : pfColor ? pfColor : undefined;
                        }

                        if (!sColor) {
                            sColor = twinkling && twinkleRgb !== undefined ? twinkleRgb : psColor ? psColor : undefined;
                        }
                    }

                    const options = this.container.actualOptions;
                    const zIndexOptions = particle.options.zIndex;
                    const zOpacityFactor = 1 - zIndexOptions.opacityRate * particle.zIndexFactor;
                    const radius = particle.getRadius();
                    const opacity = twinkling ? twinkle.opacity : (_b = particle.bubble.opacity) !== null && _b !== void 0 ? _b : particle.opacity.value;
                    const strokeOpacity = (_c = particle.stroke.opacity) !== null && _c !== void 0 ? _c : opacity;
                    const zOpacity = opacity * zOpacityFactor;
                    const fillColorValue = fColor ? getStyleFromHsl(fColor, zOpacity) : undefined;

                    if (!fillColorValue && !sColor) {
                        return;
                    }

                    const orbitOptions = particle.options.orbit;
                    this.draw(ctx => {
                        const zSizeFactor = 1 - zIndexOptions.sizeRate * particle.zIndexFactor;
                        const zStrokeOpacity = strokeOpacity * zOpacityFactor;
                        const strokeColorValue = sColor ? getStyleFromHsl(sColor, zStrokeOpacity) : fillColorValue;

                        if (radius <= 0) {
                            return;
                        }

                        if (orbitOptions.enable) {
                            this.drawOrbit(particle, OrbitType.back);
                        }

                        drawParticle(this.container, ctx, particle, delta, fillColorValue, strokeColorValue, options.backgroundMask.enable, options.backgroundMask.composite, radius * zSizeFactor, zOpacity, particle.options.shadow);

                        if (orbitOptions.enable) {
                            this.drawOrbit(particle, OrbitType.front);
                        }
                    });
                }

                drawOrbit(particle, type) {
                    const container = this.container;
                    const orbitOptions = particle.options.orbit;
                    let start;
                    let end;

                    if (type === OrbitType.back) {
                        start = Math.PI / 2;
                        end = Math.PI * 3 / 2;
                    } else if (type === OrbitType.front) {
                        start = Math.PI * 3 / 2;
                        end = Math.PI / 2;
                    } else {
                        start = 0;
                        end = 2 * Math.PI;
                    }

                    this.draw(ctx => {
                        var _a, _b, _c, _d;

                        drawEllipse(ctx, particle, (_a = particle.orbitColor) !== null && _a !== void 0 ? _a : particle.getFillColor(), (_c = (_b = particle.orbitRadius) !== null && _b !== void 0 ? _b : container.retina.orbitRadius) !== null && _c !== void 0 ? _c : particle.getRadius(), orbitOptions.opacity, orbitOptions.width, ((_d = particle.orbitRotation) !== null && _d !== void 0 ? _d : 0) * container.retina.pixelRatio, start, end);
                    });
                }

                drawPlugin(plugin, delta) {
                    this.draw(ctx => {
                        drawPlugin(ctx, plugin, delta);
                    });
                }

                drawParticlePlugin(plugin, particle, delta) {
                    this.draw(ctx => {
                        drawParticlePlugin(ctx, plugin, particle, delta);
                    });
                }

                initBackground() {
                    const options = this.container.actualOptions;
                    const background = options.background;
                    const element = this.element;
                    const elementStyle = element === null || element === void 0 ? void 0 : element.style;

                    if (!elementStyle) {
                        return;
                    }

                    if (background.color) {
                        const color = colorToRgb(background.color);
                        elementStyle.backgroundColor = color ? getStyleFromRgb(color, background.opacity) : "";
                    } else {
                        elementStyle.backgroundColor = "";
                    }

                    elementStyle.backgroundImage = background.image || "";
                    elementStyle.backgroundPosition = background.position || "";
                    elementStyle.backgroundRepeat = background.repeat || "";
                    elementStyle.backgroundSize = background.size || "";
                }

                draw(cb) {
                    if (!this.context) {
                        return;
                    }

                    return cb(this.context);
                }

                initCover() {
                    const options = this.container.actualOptions;
                    const cover = options.backgroundMask.cover;
                    const color = cover.color;
                    const coverRgb = colorToRgb(color);

                    if (coverRgb) {
                        this.coverColor = {
                            r: coverRgb.r,
                            g: coverRgb.g,
                            b: coverRgb.b,
                            a: cover.opacity
                        };
                    }
                }

                initTrail() {
                    const options = this.container.actualOptions;
                    const trail = options.particles.move.trail;
                    const fillColor = colorToRgb(trail.fillColor);

                    if (fillColor) {
                        const trail = options.particles.move.trail;
                        this.trailFillColor = {
                            r: fillColor.r,
                            g: fillColor.g,
                            b: fillColor.b,
                            a: 1 / trail.length
                        };
                    }
                }

                getPluginParticleColors(particle) {
                    let fColor;
                    let sColor;

                    for (const [, plugin] of this.container.plugins) {
                        if (!fColor && plugin.particleFillColor) {
                            fColor = colorToHsl(plugin.particleFillColor(particle));
                        }

                        if (!sColor && plugin.particleStrokeColor) {
                            sColor = colorToHsl(plugin.particleStrokeColor(particle));
                        }

                        if (fColor && sColor) {
                            break;
                        }
                    }

                    return [fColor, sColor];
                }

                initStyle() {
                    const element = this.element,
                        options = this.container.actualOptions;

                    if (!element) {
                        return;
                    }

                    const originalStyle = this.originalStyle;

                    if (options.fullScreen.enable) {
                        this.originalStyle = deepExtend({}, element.style);
                        element.style.position = "fixed";
                        element.style.zIndex = options.fullScreen.zIndex.toString(10);
                        element.style.top = "0";
                        element.style.left = "0";
                        element.style.width = "100%";
                        element.style.height = "100%";
                    } else if (originalStyle) {
                        element.style.position = originalStyle.position;
                        element.style.zIndex = originalStyle.zIndex;
                        element.style.top = originalStyle.top;
                        element.style.left = originalStyle.left;
                        element.style.width = originalStyle.width;
                        element.style.height = originalStyle.height;
                    }
                }

                paintBase(baseColor) {
                    this.draw(ctx => {
                        paintBase(ctx, this.size, baseColor);
                    });
                }

                lineStyle(p1, p2) {
                    return this.draw(ctx => {
                        const options = this.container.actualOptions;
                        const connectOptions = options.interactivity.modes.connect;
                        return gradient(ctx, p1, p2, connectOptions.links.opacity);
                    });
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/OptionsColor.js
            class OptionsColor {
                constructor() {
                    this.value = "#fff";
                }

                static create(source, data) {
                    const color = source !== null && source !== void 0 ? source : new OptionsColor();

                    if (data !== undefined) {
                        if (typeof data === "string" || data instanceof Array) {
                            color.load({
                                value: data
                            });
                        } else {
                            color.load(data);
                        }
                    }

                    return color;
                }

                load(data) {
                    if ((data === null || data === void 0 ? void 0 : data.value) === undefined) {
                        return;
                    }

                    this.value = data.value;
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/Links/LinksShadow.js

            class LinksShadow {
                constructor() {
                    this.blur = 5;
                    this.color = new OptionsColor();
                    this.enable = false;
                    this.color.value = "#00ff00";
                }

                load(data) {
                    if (data === undefined) {
                        return;
                    }

                    if (data.blur !== undefined) {
                        this.blur = data.blur;
                    }

                    this.color = OptionsColor.create(this.color, data.color);

                    if (data.enable !== undefined) {
                        this.enable = data.enable;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/Links/LinksTriangle.js

            class LinksTriangle {
                constructor() {
                    this.enable = false;
                    this.frequency = 1;
                }

                load(data) {
                    if (data === undefined) {
                        return;
                    }

                    if (data.color !== undefined) {
                        this.color = OptionsColor.create(this.color, data.color);
                    }

                    if (data.enable !== undefined) {
                        this.enable = data.enable;
                    }

                    if (data.frequency !== undefined) {
                        this.frequency = data.frequency;
                    }

                    if (data.opacity !== undefined) {
                        this.opacity = data.opacity;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/Links/Links.js



            class Links {
                constructor() {
                    this.blink = false;
                    this.color = new OptionsColor();
                    this.consent = false;
                    this.distance = 100;
                    this.enable = false;
                    this.frequency = 1;
                    this.opacity = 1;
                    this.shadow = new LinksShadow();
                    this.triangles = new LinksTriangle();
                    this.width = 1;
                    this.warp = false;
                }

                load(data) {
                    if (data === undefined) {
                        return;
                    }

                    if (data.id !== undefined) {
                        this.id = data.id;
                    }

                    if (data.blink !== undefined) {
                        this.blink = data.blink;
                    }

                    this.color = OptionsColor.create(this.color, data.color);

                    if (data.consent !== undefined) {
                        this.consent = data.consent;
                    }

                    if (data.distance !== undefined) {
                        this.distance = data.distance;
                    }

                    if (data.enable !== undefined) {
                        this.enable = data.enable;
                    }

                    if (data.frequency !== undefined) {
                        this.frequency = data.frequency;
                    }

                    if (data.opacity !== undefined) {
                        this.opacity = data.opacity;
                    }

                    this.shadow.load(data.shadow);
                    this.triangles.load(data.triangles);

                    if (data.width !== undefined) {
                        this.width = data.width;
                    }

                    if (data.warp !== undefined) {
                        this.warp = data.warp;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/Move/Attract.js
            class Attract {
                constructor() {
                    this.distance = 200;
                    this.enable = false;
                    this.rotate = {
                        x: 3000,
                        y: 3000
                    };
                }

                get rotateX() {
                    return this.rotate.x;
                }

                set rotateX(value) {
                    this.rotate.x = value;
                }

                get rotateY() {
                    return this.rotate.y;
                }

                set rotateY(value) {
                    this.rotate.y = value;
                }

                load(data) {
                    var _a, _b, _c, _d;

                    if (!data) {
                        return;
                    }

                    if (data.distance !== undefined) {
                        this.distance = data.distance;
                    }

                    if (data.enable !== undefined) {
                        this.enable = data.enable;
                    }

                    const rotateX = (_b = (_a = data.rotate) === null || _a === void 0 ? void 0 : _a.x) !== null && _b !== void 0 ? _b : data.rotateX;

                    if (rotateX !== undefined) {
                        this.rotate.x = rotateX;
                    }

                    const rotateY = (_d = (_c = data.rotate) === null || _c === void 0 ? void 0 : _c.y) !== null && _d !== void 0 ? _d : data.rotateY;

                    if (rotateY !== undefined) {
                        this.rotate.y = rotateY;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/Move/Trail.js

            class Trail {
                constructor() {
                    this.enable = false;
                    this.length = 10;
                    this.fillColor = new OptionsColor();
                    this.fillColor.value = "#000000";
                }

                load(data) {
                    if (data === undefined) {
                        return;
                    }

                    if (data.enable !== undefined) {
                        this.enable = data.enable;
                    }

                    this.fillColor = OptionsColor.create(this.fillColor, data.fillColor);

                    if (data.length !== undefined) {
                        this.length = data.length;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Random.js
            class Random {
                constructor() {
                    this.enable = false;
                    this.minimumValue = 0;
                }

                load(data) {
                    if (!data) {
                        return;
                    }

                    if (data.enable !== undefined) {
                        this.enable = data.enable;
                    }

                    if (data.minimumValue !== undefined) {
                        this.minimumValue = data.minimumValue;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/ValueWithRandom.js


            class ValueWithRandom {
                constructor() {
                    this.random = new Random();
                    this.value = 0;
                }

                load(data) {
                    if (!data) {
                        return;
                    }

                    if (typeof data.random === "boolean") {
                        this.random.enable = data.random;
                    } else {
                        this.random.load(data.random);
                    }

                    if (data.value !== undefined) {
                        this.value = setRangeValue(data.value, this.random.enable ? this.random.minimumValue : undefined);
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/Move/Path/PathDelay.js

            class PathDelay extends ValueWithRandom {
                constructor() {
                    super();
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/Move/Path/Path.js

            class Path {
                constructor() {
                    this.clamp = true;
                    this.delay = new PathDelay();
                    this.enable = false;
                }

                load(data) {
                    if (data === undefined) {
                        return;
                    }

                    if (data.clamp !== undefined) {
                        this.clamp = data.clamp;
                    }

                    this.delay.load(data.delay);

                    if (data.enable !== undefined) {
                        this.enable = data.enable;
                    }

                    this.generator = data.generator;
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/Move/MoveAngle.js
            class MoveAngle {
                constructor() {
                    this.offset = 0;
                    this.value = 90;
                }

                load(data) {
                    if (data === undefined) {
                        return;
                    }

                    if (data.offset !== undefined) {
                        this.offset = data.offset;
                    }

                    if (data.value !== undefined) {
                        this.value = data.value;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/Move/MoveGravity.js
            class MoveGravity {
                constructor() {
                    this.acceleration = 9.81;
                    this.enable = false;
                    this.inverse = false;
                    this.maxSpeed = 50;
                }

                load(data) {
                    if (!data) {
                        return;
                    }

                    if (data.acceleration !== undefined) {
                        this.acceleration = data.acceleration;
                    }

                    if (data.enable !== undefined) {
                        this.enable = data.enable;
                    }

                    if (data.inverse !== undefined) {
                        this.inverse = data.inverse;
                    }

                    if (data.maxSpeed !== undefined) {
                        this.maxSpeed = data.maxSpeed;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/Move/OutModes.js

            class OutModes {
                constructor() {
                    this.default = OutMode.out;
                }

                load(data) {
                    var _a, _b, _c, _d;

                    if (!data) {
                        return;
                    }

                    if (data.default !== undefined) {
                        this.default = data.default;
                    }

                    this.bottom = (_a = data.bottom) !== null && _a !== void 0 ? _a : data.default;
                    this.left = (_b = data.left) !== null && _b !== void 0 ? _b : data.default;
                    this.right = (_c = data.right) !== null && _c !== void 0 ? _c : data.default;
                    this.top = (_d = data.top) !== null && _d !== void 0 ? _d : data.default;
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/Move/Spin.js

            class Spin {
                constructor() {
                    this.acceleration = 0;
                    this.enable = false;
                }

                load(data) {
                    if (!data) {
                        return;
                    }

                    if (data.acceleration !== undefined) {
                        this.acceleration = setRangeValue(data.acceleration);
                    }

                    if (data.enable !== undefined) {
                        this.enable = data.enable;
                    }

                    this.position = data.position ? deepExtend({}, data.position) : undefined;
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/Move/Move.js









            class Move {
                constructor() {
                    this.angle = new MoveAngle();
                    this.attract = new Attract();
                    this.decay = 0;
                    this.distance = {};
                    this.direction = MoveDirection.none;
                    this.drift = 0;
                    this.enable = false;
                    this.gravity = new MoveGravity();
                    this.path = new Path();
                    this.outModes = new OutModes();
                    this.random = false;
                    this.size = false;
                    this.speed = 2;
                    this.spin = new Spin();
                    this.straight = false;
                    this.trail = new Trail();
                    this.vibrate = false;
                    this.warp = false;
                }

                get collisions() {
                    return false;
                }

                set collisions(value) { }

                get bounce() {
                    return this.collisions;
                }

                set bounce(value) {
                    this.collisions = value;
                }

                get out_mode() {
                    return this.outMode;
                }

                set out_mode(value) {
                    this.outMode = value;
                }

                get outMode() {
                    return this.outModes.default;
                }

                set outMode(value) {
                    this.outModes.default = value;
                }

                get noise() {
                    return this.path;
                }

                set noise(value) {
                    this.path = value;
                }

                load(data) {
                    var _a, _b, _c;

                    if (data === undefined) {
                        return;
                    }

                    if (data.angle !== undefined) {
                        if (typeof data.angle === "number") {
                            this.angle.value = data.angle;
                        } else {
                            this.angle.load(data.angle);
                        }
                    }

                    this.attract.load(data.attract);

                    if (data.decay !== undefined) {
                        this.decay = data.decay;
                    }

                    if (data.direction !== undefined) {
                        this.direction = data.direction;
                    }

                    if (data.distance !== undefined) {
                        this.distance = typeof data.distance === "number" ? {
                            horizontal: data.distance,
                            vertical: data.distance
                        } : deepExtend({}, data.distance);
                    }

                    if (data.drift !== undefined) {
                        this.drift = setRangeValue(data.drift);
                    }

                    if (data.enable !== undefined) {
                        this.enable = data.enable;
                    }

                    this.gravity.load(data.gravity);
                    const outMode = (_a = data.outMode) !== null && _a !== void 0 ? _a : data.out_mode;

                    if (data.outModes !== undefined || outMode !== undefined) {
                        if (typeof data.outModes === "string" || data.outModes === undefined && outMode !== undefined) {
                            this.outModes.load({
                                default: (_b = data.outModes) !== null && _b !== void 0 ? _b : outMode
                            });
                        } else {
                            this.outModes.load(data.outModes);
                        }
                    }

                    this.path.load((_c = data.path) !== null && _c !== void 0 ? _c : data.noise);

                    if (data.random !== undefined) {
                        this.random = data.random;
                    }

                    if (data.size !== undefined) {
                        this.size = data.size;
                    }

                    if (data.speed !== undefined) {
                        this.speed = setRangeValue(data.speed);
                    }

                    this.spin.load(data.spin);

                    if (data.straight !== undefined) {
                        this.straight = data.straight;
                    }

                    this.trail.load(data.trail);

                    if (data.vibrate !== undefined) {
                        this.vibrate = data.vibrate;
                    }

                    if (data.warp !== undefined) {
                        this.warp = data.warp;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/Number/Density.js
            class Density {
                constructor() {
                    this.enable = false;
                    this.area = 800;
                    this.factor = 1000;
                }

                get value_area() {
                    return this.area;
                }

                set value_area(value) {
                    this.area = value;
                }

                load(data) {
                    var _a;

                    if (data === undefined) {
                        return;
                    }

                    if (data.enable !== undefined) {
                        this.enable = data.enable;
                    }

                    const area = (_a = data.area) !== null && _a !== void 0 ? _a : data.value_area;

                    if (area !== undefined) {
                        this.area = area;
                    }

                    if (data.factor !== undefined) {
                        this.factor = data.factor;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/Number/ParticlesNumber.js

            class ParticlesNumber {
                constructor() {
                    this.density = new Density();
                    this.limit = 0;
                    this.value = 100;
                }

                get max() {
                    return this.limit;
                }

                set max(value) {
                    this.limit = value;
                }

                load(data) {
                    var _a;

                    if (data === undefined) {
                        return;
                    }

                    this.density.load(data.density);
                    const limit = (_a = data.limit) !== null && _a !== void 0 ? _a : data.max;

                    if (limit !== undefined) {
                        this.limit = limit;
                    }

                    if (data.value !== undefined) {
                        this.value = data.value;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/AnimationOptions.js
            class AnimationOptions {
                constructor() {
                    this.count = 0;
                    this.enable = false;
                    this.speed = 1;
                    this.sync = false;
                }

                load(data) {
                    if (!data) {
                        return;
                    }

                    if (data.count !== undefined) {
                        this.count = data.count;
                    }

                    if (data.enable !== undefined) {
                        this.enable = data.enable;
                    }

                    if (data.speed !== undefined) {
                        this.speed = data.speed;
                    }

                    if (data.sync !== undefined) {
                        this.sync = data.sync;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/Opacity/OpacityAnimation.js


            class OpacityAnimation extends AnimationOptions {
                constructor() {
                    super();
                    this.destroy = DestroyType.none;
                    this.enable = false;
                    this.minimumValue = 0;
                    this.speed = 2;
                    this.startValue = StartValueType.random;
                    this.sync = false;
                }

                get opacity_min() {
                    return this.minimumValue;
                }

                set opacity_min(value) {
                    this.minimumValue = value;
                }

                load(data) {
                    var _a;

                    if (data === undefined) {
                        return;
                    }

                    super.load(data);

                    if (data.destroy !== undefined) {
                        this.destroy = data.destroy;
                    }

                    if (data.enable !== undefined) {
                        this.enable = data.enable;
                    }

                    const minimumValue = (_a = data.minimumValue) !== null && _a !== void 0 ? _a : data.opacity_min;

                    if (minimumValue !== undefined) {
                        this.minimumValue = minimumValue;
                    }

                    if (data.speed !== undefined) {
                        this.speed = data.speed;
                    }

                    if (data.startValue !== undefined) {
                        this.startValue = data.startValue;
                    }

                    if (data.sync !== undefined) {
                        this.sync = data.sync;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/Opacity/Opacity.js



            class Opacity extends ValueWithRandom {
                constructor() {
                    super();
                    this.animation = new OpacityAnimation();
                    this.random.minimumValue = 0.1;
                    this.value = 1;
                }

                get anim() {
                    return this.animation;
                }

                set anim(value) {
                    this.animation = value;
                }

                load(data) {
                    var _a;

                    if (!data) {
                        return;
                    }

                    super.load(data);
                    const animation = (_a = data.animation) !== null && _a !== void 0 ? _a : data.anim;

                    if (animation !== undefined) {
                        this.animation.load(animation);
                        this.value = setRangeValue(this.value, this.animation.enable ? this.animation.minimumValue : undefined);
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/Shape/Shape.js


            class Shape {
                constructor() {
                    this.options = {};
                    this.type = ShapeType.circle;
                }

                get image() {
                    var _a;

                    return (_a = this.options[ShapeType.image]) !== null && _a !== void 0 ? _a : this.options[ShapeType.images];
                }

                set image(value) {
                    this.options[ShapeType.image] = value;
                    this.options[ShapeType.images] = value;
                }

                get custom() {
                    return this.options;
                }

                set custom(value) {
                    this.options = value;
                }

                get images() {
                    return this.image;
                }

                set images(value) {
                    this.image = value;
                }

                get stroke() {
                    return [];
                }

                set stroke(_value) { }

                get character() {
                    var _a;

                    return (_a = this.options[ShapeType.character]) !== null && _a !== void 0 ? _a : this.options[ShapeType.char];
                }

                set character(value) {
                    this.options[ShapeType.character] = value;
                    this.options[ShapeType.char] = value;
                }

                get polygon() {
                    var _a;

                    return (_a = this.options[ShapeType.polygon]) !== null && _a !== void 0 ? _a : this.options[ShapeType.star];
                }

                set polygon(value) {
                    this.options[ShapeType.polygon] = value;
                    this.options[ShapeType.star] = value;
                }

                load(data) {
                    var _a, _b, _c;

                    if (data === undefined) {
                        return;
                    }

                    const options = (_a = data.options) !== null && _a !== void 0 ? _a : data.custom;

                    if (options !== undefined) {
                        for (const shape in options) {
                            const item = options[shape];

                            if (item !== undefined) {
                                this.options[shape] = deepExtend((_b = this.options[shape]) !== null && _b !== void 0 ? _b : {}, item);
                            }
                        }
                    }

                    this.loadShape(data.character, ShapeType.character, ShapeType.char, true);
                    this.loadShape(data.polygon, ShapeType.polygon, ShapeType.star, false);
                    this.loadShape((_c = data.image) !== null && _c !== void 0 ? _c : data.images, ShapeType.image, ShapeType.images, true);

                    if (data.type !== undefined) {
                        this.type = data.type;
                    }
                }

                loadShape(item, mainKey, altKey, altOverride) {
                    var _a, _b, _c, _d;

                    if (item === undefined) {
                        return;
                    }

                    if (item instanceof Array) {
                        if (!(this.options[mainKey] instanceof Array)) {
                            this.options[mainKey] = [];

                            if (!this.options[altKey] || altOverride) {
                                this.options[altKey] = [];
                            }
                        }

                        this.options[mainKey] = deepExtend((_a = this.options[mainKey]) !== null && _a !== void 0 ? _a : [], item);

                        if (!this.options[altKey] || altOverride) {
                            this.options[altKey] = deepExtend((_b = this.options[altKey]) !== null && _b !== void 0 ? _b : [], item);
                        }
                    } else {
                        if (this.options[mainKey] instanceof Array) {
                            this.options[mainKey] = {};

                            if (!this.options[altKey] || altOverride) {
                                this.options[altKey] = {};
                            }
                        }

                        this.options[mainKey] = deepExtend((_c = this.options[mainKey]) !== null && _c !== void 0 ? _c : {}, item);

                        if (!this.options[altKey] || altOverride) {
                            this.options[altKey] = deepExtend((_d = this.options[altKey]) !== null && _d !== void 0 ? _d : {}, item);
                        }
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/Size/SizeAnimation.js


            class SizeAnimation extends AnimationOptions {
                constructor() {
                    super();
                    this.destroy = DestroyType.none;
                    this.enable = false;
                    this.minimumValue = 0;
                    this.speed = 5;
                    this.startValue = StartValueType.random;
                    this.sync = false;
                }

                get size_min() {
                    return this.minimumValue;
                }

                set size_min(value) {
                    this.minimumValue = value;
                }

                load(data) {
                    var _a;

                    if (data === undefined) {
                        return;
                    }

                    super.load(data);

                    if (data.destroy !== undefined) {
                        this.destroy = data.destroy;
                    }

                    if (data.enable !== undefined) {
                        this.enable = data.enable;
                    }

                    const minimumValue = (_a = data.minimumValue) !== null && _a !== void 0 ? _a : data.size_min;

                    if (minimumValue !== undefined) {
                        this.minimumValue = minimumValue;
                    }

                    if (data.speed !== undefined) {
                        this.speed = data.speed;
                    }

                    if (data.startValue !== undefined) {
                        this.startValue = data.startValue;
                    }

                    if (data.sync !== undefined) {
                        this.sync = data.sync;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/Size/Size.js



            class Size extends ValueWithRandom {
                constructor() {
                    super();
                    this.animation = new SizeAnimation();
                    this.random.minimumValue = 1;
                    this.value = 3;
                }

                get anim() {
                    return this.animation;
                }

                set anim(value) {
                    this.animation = value;
                }

                load(data) {
                    var _a;

                    if (!data) {
                        return;
                    }

                    super.load(data);
                    const animation = (_a = data.animation) !== null && _a !== void 0 ? _a : data.anim;

                    if (animation !== undefined) {
                        this.animation.load(animation);
                        this.value = setRangeValue(this.value, this.animation.enable ? this.animation.minimumValue : undefined);
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/Rotate/RotateAnimation.js
            class RotateAnimation {
                constructor() {
                    this.enable = false;
                    this.speed = 0;
                    this.sync = false;
                }

                load(data) {
                    if (data === undefined) {
                        return;
                    }

                    if (data.enable !== undefined) {
                        this.enable = data.enable;
                    }

                    if (data.speed !== undefined) {
                        this.speed = data.speed;
                    }

                    if (data.sync !== undefined) {
                        this.sync = data.sync;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/Rotate/Rotate.js



            class Rotate extends ValueWithRandom {
                constructor() {
                    super();
                    this.animation = new RotateAnimation();
                    this.direction = RotateDirection.clockwise;
                    this.path = false;
                    this.value = 0;
                }

                load(data) {
                    if (!data) {
                        return;
                    }

                    super.load(data);

                    if (data.direction !== undefined) {
                        this.direction = data.direction;
                    }

                    this.animation.load(data.animation);

                    if (data.path !== undefined) {
                        this.path = data.path;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/Shadow.js

            class Shadow {
                constructor() {
                    this.blur = 0;
                    this.color = new OptionsColor();
                    this.enable = false;
                    this.offset = {
                        x: 0,
                        y: 0
                    };
                    this.color.value = "#000000";
                }

                load(data) {
                    if (data === undefined) {
                        return;
                    }

                    if (data.blur !== undefined) {
                        this.blur = data.blur;
                    }

                    this.color = OptionsColor.create(this.color, data.color);

                    if (data.enable !== undefined) {
                        this.enable = data.enable;
                    }

                    if (data.offset === undefined) {
                        return;
                    }

                    if (data.offset.x !== undefined) {
                        this.offset.x = data.offset.x;
                    }

                    if (data.offset.y !== undefined) {
                        this.offset.y = data.offset.y;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/ColorAnimation.js

            class ColorAnimation {
                constructor() {
                    this.count = 0;
                    this.enable = false;
                    this.offset = 0;
                    this.speed = 1;
                    this.sync = true;
                }

                load(data) {
                    if (data === undefined) {
                        return;
                    }

                    if (data.count !== undefined) {
                        this.count = data.count;
                    }

                    if (data.enable !== undefined) {
                        this.enable = data.enable;
                    }

                    if (data.offset !== undefined) {
                        this.offset = setRangeValue(data.offset);
                    }

                    if (data.speed !== undefined) {
                        this.speed = data.speed;
                    }

                    if (data.sync !== undefined) {
                        this.sync = data.sync;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/HslAnimation.js

            class HslAnimation {
                constructor() {
                    this.h = new ColorAnimation();
                    this.s = new ColorAnimation();
                    this.l = new ColorAnimation();
                }

                load(data) {
                    if (!data) {
                        return;
                    }

                    this.h.load(data.h);
                    this.s.load(data.s);
                    this.l.load(data.l);
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/AnimatableColor.js


            class AnimatableColor extends OptionsColor {
                constructor() {
                    super();
                    this.animation = new HslAnimation();
                }

                static create(source, data) {
                    const color = source !== null && source !== void 0 ? source : new AnimatableColor();

                    if (data !== undefined) {
                        color.load(typeof data === "string" ? {
                            value: data
                        } : data);
                    }

                    return color;
                }

                load(data) {
                    super.load(data);

                    if (!data) {
                        return;
                    }

                    const colorAnimation = data.animation;

                    if (colorAnimation !== undefined) {
                        if (colorAnimation.enable !== undefined) {
                            this.animation.h.load(colorAnimation);
                        } else {
                            this.animation.load(data.animation);
                        }
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/Stroke.js

            class Stroke {
                constructor() {
                    this.width = 0;
                }

                load(data) {
                    if (data === undefined) {
                        return;
                    }

                    if (data.color !== undefined) {
                        this.color = AnimatableColor.create(this.color, data.color);
                    }

                    if (data.width !== undefined) {
                        this.width = data.width;
                    }

                    if (data.opacity !== undefined) {
                        this.opacity = data.opacity;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/Bounce/BounceFactor.js

            class BounceFactor extends ValueWithRandom {
                constructor() {
                    super();
                    this.random.minimumValue = 0.1;
                    this.value = 1;
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/Bounce/Bounce.js

            class Bounce {
                constructor() {
                    this.horizontal = new BounceFactor();
                    this.vertical = new BounceFactor();
                }

                load(data) {
                    if (!data) {
                        return;
                    }

                    this.horizontal.load(data.horizontal);
                    this.vertical.load(data.vertical);
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/Collisions/CollisionsOverlap.js
            class CollisionsOverlap {
                constructor() {
                    this.enable = true;
                    this.retries = 0;
                }

                load(data) {
                    if (!data) {
                        return;
                    }

                    if (data.enable !== undefined) {
                        this.enable = data.enable;
                    }

                    if (data.retries !== undefined) {
                        this.retries = data.retries;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/Collisions/Collisions.js



            class Collisions {
                constructor() {
                    this.bounce = new Bounce();
                    this.enable = false;
                    this.mode = CollisionMode.bounce;
                    this.overlap = new CollisionsOverlap();
                }

                load(data) {
                    if (data === undefined) {
                        return;
                    }

                    this.bounce.load(data.bounce);

                    if (data.enable !== undefined) {
                        this.enable = data.enable;
                    }

                    if (data.mode !== undefined) {
                        this.mode = data.mode;
                    }

                    this.overlap.load(data.overlap);
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/Twinkle/TwinkleValues.js

            class TwinkleValues {
                constructor() {
                    this.enable = false;
                    this.frequency = 0.05;
                    this.opacity = 1;
                }

                load(data) {
                    if (data === undefined) {
                        return;
                    }

                    if (data.color !== undefined) {
                        this.color = OptionsColor.create(this.color, data.color);
                    }

                    if (data.enable !== undefined) {
                        this.enable = data.enable;
                    }

                    if (data.frequency !== undefined) {
                        this.frequency = data.frequency;
                    }

                    if (data.opacity !== undefined) {
                        this.opacity = data.opacity;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/Twinkle/Twinkle.js

            class Twinkle {
                constructor() {
                    this.lines = new TwinkleValues();
                    this.particles = new TwinkleValues();
                }

                load(data) {
                    if (data === undefined) {
                        return;
                    }

                    this.lines.load(data.lines);
                    this.particles.load(data.particles);
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/Life/LifeDelay.js

            class LifeDelay extends ValueWithRandom {
                constructor() {
                    super();
                    this.sync = false;
                }

                load(data) {
                    if (!data) {
                        return;
                    }

                    super.load(data);

                    if (data.sync !== undefined) {
                        this.sync = data.sync;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/Life/LifeDuration.js

            class LifeDuration extends ValueWithRandom {
                constructor() {
                    super();
                    this.random.minimumValue = 0.0001;
                    this.sync = false;
                }

                load(data) {
                    if (data === undefined) {
                        return;
                    }

                    super.load(data);

                    if (data.sync !== undefined) {
                        this.sync = data.sync;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/Life/Life.js


            class Life {
                constructor() {
                    this.count = 0;
                    this.delay = new LifeDelay();
                    this.duration = new LifeDuration();
                }

                load(data) {
                    if (data === undefined) {
                        return;
                    }

                    if (data.count !== undefined) {
                        this.count = data.count;
                    }

                    this.delay.load(data.delay);
                    this.duration.load(data.duration);
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/Destroy/SplitFactor.js

            class SplitFactor extends ValueWithRandom {
                constructor() {
                    super();
                    this.value = 3;
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/Destroy/SplitRate.js

            class SplitRate extends ValueWithRandom {
                constructor() {
                    super();
                    this.value = {
                        min: 4,
                        max: 9
                    };
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/Destroy/Split.js



            class Split {
                constructor() {
                    this.count = 1;
                    this.factor = new SplitFactor();
                    this.rate = new SplitRate();
                    this.sizeOffset = true;
                }

                load(data) {
                    if (!data) {
                        return;
                    }

                    if (data.count !== undefined) {
                        this.count = data.count;
                    }

                    this.factor.load(data.factor);
                    this.rate.load(data.rate);

                    if (data.particles !== undefined) {
                        this.particles = deepExtend({}, data.particles);
                    }

                    if (data.sizeOffset !== undefined) {
                        this.sizeOffset = data.sizeOffset;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/Destroy/Destroy.js


            class Destroy {
                constructor() {
                    this.mode = DestroyMode.none;
                    this.split = new Split();
                }

                load(data) {
                    if (!data) {
                        return;
                    }

                    if (data.mode !== undefined) {
                        this.mode = data.mode;
                    }

                    this.split.load(data.split);
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/Wobble/Wobble.js

            class Wobble {
                constructor() {
                    this.distance = 5;
                    this.enable = false;
                    this.speed = 50;
                }

                load(data) {
                    if (!data) {
                        return;
                    }

                    if (data.distance !== undefined) {
                        this.distance = setRangeValue(data.distance);
                    }

                    if (data.enable !== undefined) {
                        this.enable = data.enable;
                    }

                    if (data.speed !== undefined) {
                        this.speed = setRangeValue(data.speed);
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/Tilt/TiltAnimation.js
            class TiltAnimation {
                constructor() {
                    this.enable = false;
                    this.speed = 0;
                    this.sync = false;
                }

                load(data) {
                    if (data === undefined) {
                        return;
                    }

                    if (data.enable !== undefined) {
                        this.enable = data.enable;
                    }

                    if (data.speed !== undefined) {
                        this.speed = data.speed;
                    }

                    if (data.sync !== undefined) {
                        this.sync = data.sync;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/Tilt/Tilt.js



            class Tilt extends ValueWithRandom {
                constructor() {
                    super();
                    this.animation = new TiltAnimation();
                    this.direction = TiltDirection.clockwise;
                    this.enable = false;
                    this.value = 0;
                }

                load(data) {
                    if (!data) {
                        return;
                    }

                    super.load(data);
                    this.animation.load(data.animation);

                    if (data.direction !== undefined) {
                        this.direction = data.direction;
                    }

                    if (data.enable !== undefined) {
                        this.enable = data.enable;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/Roll/RollLight.js
            class RollLight {
                constructor() {
                    this.enable = false;
                    this.value = 0;
                }

                load(data) {
                    if (!data) {
                        return;
                    }

                    if (data.enable !== undefined) {
                        this.enable = data.enable;
                    }

                    if (data.value !== undefined) {
                        this.value = data.value;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/Roll/Roll.js



            class Roll {
                constructor() {
                    this.darken = new RollLight();
                    this.enable = false;
                    this.enlighten = new RollLight();
                    this.speed = 25;
                }

                load(data) {
                    if (!data) {
                        return;
                    }

                    if (data.backColor !== undefined) {
                        this.backColor = OptionsColor.create(this.backColor, data.backColor);
                    }

                    this.darken.load(data.darken);

                    if (data.enable !== undefined) {
                        this.enable = data.enable;
                    }

                    this.enlighten.load(data.enlighten);

                    if (data.speed !== undefined) {
                        this.speed = setRangeValue(data.speed);
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/ZIndex/ZIndex.js

            class ZIndex extends ValueWithRandom {
                constructor() {
                    super();
                    this.opacityRate = 1;
                    this.sizeRate = 1;
                    this.velocityRate = 1;
                }

                load(data) {
                    super.load(data);

                    if (!data) {
                        return;
                    }

                    if (data.opacityRate !== undefined) {
                        this.opacityRate = data.opacityRate;
                    }

                    if (data.sizeRate !== undefined) {
                        this.sizeRate = data.sizeRate;
                    }

                    if (data.velocityRate !== undefined) {
                        this.velocityRate = data.velocityRate;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/Orbit/OrbitRotation.js

            class OrbitRotation extends ValueWithRandom {
                constructor() {
                    super();
                    this.value = 45;
                    this.random.enable = false;
                    this.random.minimumValue = 0;
                }

                load(data) {
                    if (data === undefined) {
                        return;
                    }

                    super.load(data);
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/Orbit/Orbit.js



            class Orbit {
                constructor() {
                    this.animation = new AnimationOptions();
                    this.enable = false;
                    this.opacity = 1;
                    this.rotation = new OrbitRotation();
                    this.width = 1;
                }

                load(data) {
                    if (data === undefined) {
                        return;
                    }

                    this.animation.load(data.animation);
                    this.rotation.load(data.rotation);

                    if (data.enable !== undefined) {
                        this.enable = data.enable;
                    }

                    if (data.opacity !== undefined) {
                        this.opacity = data.opacity;
                    }

                    if (data.width !== undefined) {
                        this.width = data.width;
                    }

                    if (data.radius !== undefined) {
                        this.radius = data.radius;
                    }

                    if (data.color !== undefined) {
                        this.color = OptionsColor.create(this.color, data.color);
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/Repulse/Repulse.js

            class Repulse extends ValueWithRandom {
                constructor() {
                    super();
                    this.enabled = false;
                    this.distance = 1;
                    this.duration = 1;
                    this.factor = 1;
                    this.speed = 1;
                }

                load(data) {
                    super.load(data);

                    if (!data) {
                        return;
                    }

                    if (data.enabled !== undefined) {
                        this.enabled = data.enabled;
                    }

                    if (data.distance !== undefined) {
                        this.distance = data.distance;
                    }

                    if (data.duration !== undefined) {
                        this.duration = data.duration;
                    }

                    if (data.factor !== undefined) {
                        this.factor = data.factor;
                    }

                    if (data.speed !== undefined) {
                        this.speed = data.speed;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Particles/ParticlesOptions.js






















            class ParticlesOptions {
                constructor() {
                    this.bounce = new Bounce();
                    this.collisions = new Collisions();
                    this.color = new AnimatableColor();
                    this.destroy = new Destroy();
                    this.groups = {};
                    this.life = new Life();
                    this.links = new Links();
                    this.move = new Move();
                    this.number = new ParticlesNumber();
                    this.opacity = new Opacity();
                    this.orbit = new Orbit();
                    this.reduceDuplicates = false;
                    this.repulse = new Repulse();
                    this.roll = new Roll();
                    this.rotate = new Rotate();
                    this.shadow = new Shadow();
                    this.shape = new Shape();
                    this.size = new Size();
                    this.stroke = new Stroke();
                    this.tilt = new Tilt();
                    this.twinkle = new Twinkle();
                    this.wobble = new Wobble();
                    this.zIndex = new ZIndex();
                }

                get line_linked() {
                    return this.links;
                }

                set line_linked(value) {
                    this.links = value;
                }

                get lineLinked() {
                    return this.links;
                }

                set lineLinked(value) {
                    this.links = value;
                }

                load(data) {
                    var _a, _b, _c, _d, _e, _f, _g, _h;

                    if (data === undefined) {
                        return;
                    }

                    this.bounce.load(data.bounce);
                    this.color = AnimatableColor.create(this.color, data.color);
                    this.destroy.load(data.destroy);
                    this.life.load(data.life);
                    const links = (_b = (_a = data.links) !== null && _a !== void 0 ? _a : data.lineLinked) !== null && _b !== void 0 ? _b : data.line_linked;

                    if (links !== undefined) {
                        this.links.load(links);
                    }

                    if (data.groups !== undefined) {
                        for (const group in data.groups) {
                            const item = data.groups[group];

                            if (item !== undefined) {
                                this.groups[group] = deepExtend((_c = this.groups[group]) !== null && _c !== void 0 ? _c : {}, item);
                            }
                        }
                    }

                    this.move.load(data.move);
                    this.number.load(data.number);
                    this.opacity.load(data.opacity);
                    this.orbit.load(data.orbit);

                    if (data.reduceDuplicates !== undefined) {
                        this.reduceDuplicates = data.reduceDuplicates;
                    }

                    this.repulse.load(data.repulse);
                    this.roll.load(data.roll);
                    this.rotate.load(data.rotate);
                    this.shape.load(data.shape);
                    this.size.load(data.size);
                    this.shadow.load(data.shadow);
                    this.tilt.load(data.tilt);
                    this.twinkle.load(data.twinkle);
                    this.wobble.load(data.wobble);
                    this.zIndex.load(data.zIndex);
                    const collisions = (_e = (_d = data.move) === null || _d === void 0 ? void 0 : _d.collisions) !== null && _e !== void 0 ? _e : (_f = data.move) === null || _f === void 0 ? void 0 : _f.bounce;

                    if (collisions !== undefined) {
                        this.collisions.enable = collisions;
                    }

                    this.collisions.load(data.collisions);
                    const strokeToLoad = (_g = data.stroke) !== null && _g !== void 0 ? _g : (_h = data.shape) === null || _h === void 0 ? void 0 : _h.stroke;

                    if (strokeToLoad === undefined) {
                        return;
                    }

                    if (strokeToLoad instanceof Array) {
                        this.stroke = strokeToLoad.map(s => {
                            const tmp = new Stroke();
                            tmp.load(s);
                            return tmp;
                        });
                    } else {
                        if (this.stroke instanceof Array) {
                            this.stroke = new Stroke();
                        }

                        this.stroke.load(strokeToLoad);
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Core/Particle/Vector3d.js

            class Vector3d extends Vector {
                constructor(x, y, z) {
                    super(x, y);
                    this.z = z === undefined ? x.z : z;
                }

                static clone(source) {
                    return Vector3d.create(source.x, source.y, source.z);
                }

                static create(x, y, z) {
                    return new Vector3d(x, y, z);
                }

                add(v) {
                    return v instanceof Vector3d ? Vector3d.create(this.x + v.x, this.y + v.y, this.z + v.z) : super.add(v);
                }

                addTo(v) {
                    super.addTo(v);

                    if (v instanceof Vector3d) {
                        this.z += v.z;
                    }
                }

                sub(v) {
                    return v instanceof Vector3d ? Vector3d.create(this.x - v.x, this.y - v.y, this.z - v.z) : super.sub(v);
                }

                subFrom(v) {
                    super.subFrom(v);

                    if (v instanceof Vector3d) {
                        this.z -= v.z;
                    }
                }

                mult(n) {
                    return Vector3d.create(this.x * n, this.y * n, this.z * n);
                }

                multTo(n) {
                    super.multTo(n);
                    this.z *= n;
                }

                div(n) {
                    return Vector3d.create(this.x / n, this.y / n, this.z / n);
                }

                divTo(n) {
                    super.divTo(n);
                    this.z /= n;
                }

                copy() {
                    return Vector3d.clone(this);
                }

                setTo(v) {
                    super.setTo(v);

                    if (v instanceof Vector3d) {
                        this.z = v.z;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Core/Particle.js






            class Particle {
                constructor(id, container, position, overrideOptions, group) {
                    var _a, _b, _c, _d, _e, _f, _g, _h, _j, _k;

                    this.id = id;
                    this.container = container;
                    this.group = group;
                    this.fill = true;
                    this.close = true;
                    this.lastPathTime = 0;
                    this.destroyed = false;
                    this.unbreakable = false;
                    this.splitCount = 0;
                    this.misplaced = false;
                    this.loops = {
                        opacity: 0,
                        size: 0
                    };
                    this.maxDistance = {};
                    const pxRatio = container.retina.pixelRatio;
                    const options = container.actualOptions;
                    const particlesOptions = new ParticlesOptions();
                    particlesOptions.load(options.particles);
                    const shapeType = particlesOptions.shape.type;
                    const reduceDuplicates = particlesOptions.reduceDuplicates;
                    this.shape = shapeType instanceof Array ? itemFromArray(shapeType, this.id, reduceDuplicates) : shapeType;

                    if (overrideOptions === null || overrideOptions === void 0 ? void 0 : overrideOptions.shape) {
                        if (overrideOptions.shape.type) {
                            const overrideShapeType = overrideOptions.shape.type;
                            this.shape = overrideShapeType instanceof Array ? itemFromArray(overrideShapeType, this.id, reduceDuplicates) : overrideShapeType;
                        }

                        const shapeOptions = new Shape();
                        shapeOptions.load(overrideOptions.shape);

                        if (this.shape) {
                            this.shapeData = this.loadShapeData(shapeOptions, reduceDuplicates);
                        }
                    } else {
                        this.shapeData = this.loadShapeData(particlesOptions.shape, reduceDuplicates);
                    }

                    if (overrideOptions !== undefined) {
                        particlesOptions.load(overrideOptions);
                    }

                    if (((_a = this.shapeData) === null || _a === void 0 ? void 0 : _a.particles) !== undefined) {
                        particlesOptions.load((_b = this.shapeData) === null || _b === void 0 ? void 0 : _b.particles);
                    }

                    this.fill = (_d = (_c = this.shapeData) === null || _c === void 0 ? void 0 : _c.fill) !== null && _d !== void 0 ? _d : this.fill;
                    this.close = (_f = (_e = this.shapeData) === null || _e === void 0 ? void 0 : _e.close) !== null && _f !== void 0 ? _f : this.close;
                    this.options = particlesOptions;
                    const zIndexValue = getRangeValue(this.options.zIndex.value);
                    this.pathDelay = getValue(this.options.move.path.delay) * 1000;
                    this.wobbleDistance = 0;
                    container.retina.initParticle(this);
                    const color = this.options.color;
                    const sizeOptions = this.options.size;
                    const sizeValue = getValue(sizeOptions) * container.retina.pixelRatio;
                    this.size = {
                        value: sizeValue,
                        max: getRangeMax(sizeOptions.value) * pxRatio,
                        min: getRangeMin(sizeOptions.value) * pxRatio
                    };
                    const sizeAnimation = sizeOptions.animation;

                    if (sizeAnimation.enable) {
                        this.size.status = AnimationStatus.increasing;
                        const sizeRange = setRangeValue(sizeOptions.value, sizeAnimation.minimumValue * pxRatio);
                        this.size.min = getRangeMin(sizeRange);
                        this.size.max = getRangeMax(sizeRange);

                        switch (sizeAnimation.startValue) {
                            case StartValueType.min:
                                this.size.value = this.size.min;
                                this.size.status = AnimationStatus.increasing;
                                break;

                            case StartValueType.random:
                                this.size.value = randomInRange(this.size);
                                this.size.status = Math.random() >= 0.5 ? AnimationStatus.increasing : AnimationStatus.decreasing;
                                break;

                            case StartValueType.max:
                            default:
                                this.size.value = this.size.max;
                                this.size.status = AnimationStatus.decreasing;
                                break;
                        }

                        this.size.velocity = ((_g = this.sizeAnimationSpeed) !== null && _g !== void 0 ? _g : container.retina.sizeAnimationSpeed) / 100 * container.retina.reduceFactor;

                        if (!sizeAnimation.sync) {
                            this.size.velocity *= Math.random();
                        }
                    }

                    this.direction = getParticleDirectionAngle(this.options.move.direction);
                    this.bubble = {
                        inRange: false
                    };
                    this.initialVelocity = this.calculateVelocity();
                    this.velocity = this.initialVelocity.copy();
                    const rotateOptions = this.options.rotate;
                    this.rotate = {
                        value: getRangeValue(rotateOptions.value) * Math.PI / 180
                    };
                    let rotateDirection = rotateOptions.direction;

                    if (rotateDirection === RotateDirection.random) {
                        const index = Math.floor(Math.random() * 2);
                        rotateDirection = index > 0 ? RotateDirection.counterClockwise : RotateDirection.clockwise;
                    }

                    switch (rotateDirection) {
                        case RotateDirection.counterClockwise:
                        case "counterClockwise":
                            this.rotate.status = AnimationStatus.decreasing;
                            break;

                        case RotateDirection.clockwise:
                            this.rotate.status = AnimationStatus.increasing;
                            break;
                    }

                    const rotateAnimation = this.options.rotate.animation;

                    if (rotateAnimation.enable) {
                        this.rotate.velocity = rotateAnimation.speed / 360 * container.retina.reduceFactor;

                        if (!rotateAnimation.sync) {
                            this.rotate.velocity *= Math.random();
                        }
                    }

                    const tiltOptions = this.options.tilt;
                    this.tilt = {
                        value: getRangeValue(tiltOptions.value) * Math.PI / 180,
                        sinDirection: Math.random() >= 0.5 ? 1 : -1,
                        cosDirection: Math.random() >= 0.5 ? 1 : -1
                    };
                    let tiltDirection = tiltOptions.direction;

                    if (tiltDirection === TiltDirection.random) {
                        const index = Math.floor(Math.random() * 2);
                        tiltDirection = index > 0 ? TiltDirection.counterClockwise : TiltDirection.clockwise;
                    }

                    switch (tiltDirection) {
                        case TiltDirection.counterClockwise:
                        case "counterClockwise":
                            this.tilt.status = AnimationStatus.decreasing;
                            break;

                        case TiltDirection.clockwise:
                            this.tilt.status = AnimationStatus.increasing;
                            break;
                    }

                    const tiltAnimation = this.options.tilt.animation;

                    if (tiltAnimation.enable) {
                        this.tilt.velocity = tiltAnimation.speed / 360 * container.retina.reduceFactor;

                        if (!tiltAnimation.sync) {
                            this.tilt.velocity *= Math.random();
                        }
                    }

                    const orbitOptions = particlesOptions.orbit;

                    if (orbitOptions.enable) {
                        this.orbitRotation = getRangeValue(orbitOptions.rotation.value);
                        this.orbitColor = colorToHsl(orbitOptions.color);
                    }

                    const hslColor = colorToHsl(color, this.id, reduceDuplicates);

                    if (hslColor) {
                        this.color = {
                            h: {
                                value: hslColor.h
                            },
                            s: {
                                value: hslColor.s
                            },
                            l: {
                                value: hslColor.l
                            }
                        };
                        const colorAnimation = this.options.color.animation;
                        this.setColorAnimation(colorAnimation.h, this.color.h);
                        this.setColorAnimation(colorAnimation.s, this.color.s);
                        this.setColorAnimation(colorAnimation.l, this.color.l);
                    }

                    const rollOpt = this.options.roll;

                    if (rollOpt.enable) {
                        if (this.color) {
                            if (rollOpt.backColor) {
                                this.backColor = colorToHsl(rollOpt.backColor);
                            } else if (rollOpt.darken.enable && rollOpt.enlighten.enable) {
                                this.alterType = Math.random() >= 0.5 ? AlterType.darken : AlterType.enlighten;
                                this.alterValue = this.alterType === AlterType.darken ? rollOpt.darken.value : rollOpt.enlighten.value;
                            } else if (rollOpt.darken.enable) {
                                this.alterType = AlterType.darken;
                                this.alterValue = rollOpt.darken.value;
                            } else if (rollOpt.enlighten.enable) {
                                this.alterType = AlterType.enlighten;
                                this.alterValue = rollOpt.enlighten.value;
                            }
                        }

                        this.rollAngle = Math.random() * Math.PI * 2;
                        this.rollSpeed = getRangeValue(rollOpt.speed) / 360;
                    } else {
                        this.rollAngle = 0;
                        this.rollSpeed = 0;
                    }

                    const wobbleOpt = this.options.wobble;

                    if (wobbleOpt.enable) {
                        this.wobbleAngle = Math.random() * Math.PI * 2;
                        this.wobbleSpeed = getRangeValue(wobbleOpt.speed) / 360;
                    } else {
                        this.wobbleAngle = 0;
                        this.wobbleSpeed = 0;
                    }

                    this.position = this.calcPosition(container, position, clamp(zIndexValue, 0, container.zLayers));
                    this.initialPosition = this.position.copy();
                    this.offset = Vector.origin;
                    const particles = container.particles;
                    particles.needsSort = particles.needsSort || particles.lastZIndex < this.position.z;
                    particles.lastZIndex = this.position.z;
                    this.zIndexFactor = this.position.z / container.zLayers;
                    const opacityOptions = this.options.opacity;
                    this.opacity = {
                        max: getRangeMax(opacityOptions.value),
                        min: getRangeMin(opacityOptions.value),
                        value: getValue(opacityOptions)
                    };
                    const opacityAnimation = opacityOptions.animation;

                    if (opacityAnimation.enable) {
                        this.opacity.status = AnimationStatus.increasing;
                        const opacityRange = setRangeValue(opacityOptions.value, opacityAnimation.minimumValue);
                        this.opacity.min = getRangeMin(opacityRange);
                        this.opacity.max = getRangeMax(opacityRange);

                        switch (opacityAnimation.startValue) {
                            case StartValueType.min:
                                this.opacity.value = this.opacity.min;
                                this.opacity.status = AnimationStatus.increasing;
                                break;

                            case StartValueType.random:
                                this.opacity.value = randomInRange(this.opacity);
                                this.opacity.status = Math.random() >= 0.5 ? AnimationStatus.increasing : AnimationStatus.decreasing;
                                break;

                            case StartValueType.max:
                            default:
                                this.opacity.value = this.opacity.max;
                                this.opacity.status = AnimationStatus.decreasing;
                                break;
                        }

                        this.opacity.velocity = opacityAnimation.speed / 100 * container.retina.reduceFactor;

                        if (!opacityAnimation.sync) {
                            this.opacity.velocity *= Math.random();
                        }
                    }

                    this.sides = 24;
                    let drawer = container.drawers.get(this.shape);

                    if (!drawer) {
                        drawer = Plugins.getShapeDrawer(this.shape);

                        if (drawer) {
                            container.drawers.set(this.shape, drawer);
                        }
                    }

                    if (drawer === null || drawer === void 0 ? void 0 : drawer.loadShape) {
                        drawer === null || drawer === void 0 ? void 0 : drawer.loadShape(this);
                    }

                    const sideCountFunc = drawer === null || drawer === void 0 ? void 0 : drawer.getSidesCount;

                    if (sideCountFunc) {
                        this.sides = sideCountFunc(this);
                    }

                    this.stroke = this.options.stroke instanceof Array ? itemFromArray(this.options.stroke, this.id, reduceDuplicates) : this.options.stroke;
                    this.strokeWidth = this.stroke.width * container.retina.pixelRatio;
                    const strokeHslColor = (_h = colorToHsl(this.stroke.color)) !== null && _h !== void 0 ? _h : this.getFillColor();

                    if (strokeHslColor) {
                        this.strokeColor = {
                            h: {
                                value: strokeHslColor.h
                            },
                            s: {
                                value: strokeHslColor.s
                            },
                            l: {
                                value: strokeHslColor.l
                            }
                        };
                        const strokeColorAnimation = (_j = this.stroke.color) === null || _j === void 0 ? void 0 : _j.animation;

                        if (strokeColorAnimation && this.strokeColor) {
                            this.setColorAnimation(strokeColorAnimation.h, this.strokeColor.h);
                            this.setColorAnimation(strokeColorAnimation.s, this.strokeColor.s);
                            this.setColorAnimation(strokeColorAnimation.l, this.strokeColor.l);
                        }
                    }

                    this.life = this.loadLife();
                    this.spawning = this.life.delay > 0;

                    if (this.options.move.spin.enable) {
                        const spinPos = (_k = this.options.move.spin.position) !== null && _k !== void 0 ? _k : {
                            x: 50,
                            y: 50
                        };
                        const spinCenter = {
                            x: spinPos.x / 100 * container.canvas.size.width,
                            y: spinPos.y / 100 * container.canvas.size.height
                        };
                        const pos = this.getPosition();
                        const distance = getDistance(pos, spinCenter);
                        this.spin = {
                            center: spinCenter,
                            direction: this.velocity.x >= 0 ? RotateDirection.clockwise : RotateDirection.counterClockwise,
                            angle: this.velocity.angle,
                            radius: distance,
                            acceleration: getRangeValue(this.options.move.spin.acceleration)
                        };
                    }

                    this.shadowColor = colorToRgb(this.options.shadow.color);

                    if (drawer && drawer.particleInit) {
                        drawer.particleInit(container, this);
                    }

                    for (const [, plugin] of container.plugins) {
                        if (plugin.particleCreated) {
                            plugin.particleCreated(this);
                        }
                    }
                }

                draw(delta) {
                    const container = this.container;

                    for (const [, plugin] of container.plugins) {
                        container.canvas.drawParticlePlugin(plugin, this, delta);
                    }

                    container.canvas.drawParticle(this, delta);
                }

                getPosition() {
                    return {
                        x: this.position.x + this.offset.x,
                        y: this.position.y + this.offset.y,
                        z: this.position.z
                    };
                }

                getRadius() {
                    return this.bubble.radius || this.size.value;
                }

                getMass() {
                    const radius = this.getRadius();
                    return Math.pow(radius, 2) * Math.PI / 2;
                }

                getFillColor() {
                    if (this.bubble.color) {
                        return this.bubble.color;
                    }

                    const color = getHslFromAnimation(this.color);

                    if (color && (this.backColor || this.alterType && this.alterValue !== undefined)) {
                        const rolled = Math.floor(this.rollAngle / (Math.PI / 2)) % 2;

                        if (rolled) {
                            if (this.backColor) {
                                return this.backColor;
                            } else if (this.alterType && this.alterValue !== undefined) {
                                return {
                                    h: color.h,
                                    s: color.s,
                                    l: color.l + (this.alterType === AlterType.darken ? -1 : 1) * this.alterValue
                                };
                            }
                        }
                    }

                    return color;
                }

                getStrokeColor() {
                    var _a, _b;

                    return (_b = (_a = this.bubble.color) !== null && _a !== void 0 ? _a : getHslFromAnimation(this.strokeColor)) !== null && _b !== void 0 ? _b : this.getFillColor();
                }

                destroy(override) {
                    this.destroyed = true;
                    this.bubble.inRange = false;

                    if (this.unbreakable) {
                        return;
                    }

                    this.destroyed = true;
                    this.bubble.inRange = false;

                    for (const [, plugin] of this.container.plugins) {
                        if (plugin.particleDestroyed) {
                            plugin.particleDestroyed(this, override);
                        }
                    }

                    if (override) {
                        return;
                    }

                    const destroyOptions = this.options.destroy;

                    if (destroyOptions.mode === DestroyMode.split) {
                        this.split();
                    }
                }

                reset() {
                    this.loops.opacity = 0;
                    this.loops.size = 0;
                }

                split() {
                    const splitOptions = this.options.destroy.split;

                    if (splitOptions.count >= 0 && this.splitCount++ > splitOptions.count) {
                        return;
                    }

                    const rate = getRangeValue(splitOptions.rate.value);

                    for (let i = 0; i < rate; i++) {
                        this.container.particles.addSplitParticle(this);
                    }
                }

                setColorAnimation(colorAnimation, colorValue) {
                    if (colorAnimation.enable) {
                        colorValue.velocity = colorAnimation.speed / 100 * this.container.retina.reduceFactor;

                        if (colorAnimation.sync) {
                            return;
                        }

                        colorValue.status = AnimationStatus.increasing;
                        colorValue.velocity *= Math.random();

                        if (colorValue.value) {
                            colorValue.value *= Math.random();
                        }
                    } else {
                        colorValue.velocity = 0;
                    }
                }

                calcPosition(container, position, zIndex, tryCount = 0) {
                    var _a, _b;

                    for (const [, plugin] of container.plugins) {
                        const pluginPos = plugin.particlePosition !== undefined ? plugin.particlePosition(position, this) : undefined;

                        if (pluginPos !== undefined) {
                            return Vector3d.create(pluginPos.x, pluginPos.y, zIndex);
                        }
                    }

                    const canvasSize = container.canvas.size;
                    const pos = Vector3d.create((_a = position === null || position === void 0 ? void 0 : position.x) !== null && _a !== void 0 ? _a : Math.random() * canvasSize.width, (_b = position === null || position === void 0 ? void 0 : position.y) !== null && _b !== void 0 ? _b : Math.random() * canvasSize.height, zIndex);
                    const outMode = this.options.move.outMode;

                    if (isInArray(outMode, OutMode.bounce) || isInArray(outMode, OutMode.bounceHorizontal)) {
                        if (pos.x > container.canvas.size.width - this.size.value * 2) {
                            pos.x -= this.size.value;
                        } else if (pos.x < this.size.value * 2) {
                            pos.x += this.size.value;
                        }
                    }

                    if (isInArray(outMode, OutMode.bounce) || isInArray(outMode, OutMode.bounceVertical)) {
                        if (pos.y > container.canvas.size.height - this.size.value * 2) {
                            pos.y -= this.size.value;
                        } else if (pos.y < this.size.value * 2) {
                            pos.y += this.size.value;
                        }
                    }

                    if (this.checkOverlap(pos, tryCount)) {
                        return this.calcPosition(container, undefined, zIndex, tryCount + 1);
                    }

                    return pos;
                }

                checkOverlap(pos, tryCount = 0) {
                    const overlapOptions = this.options.collisions.overlap;

                    if (!overlapOptions.enable) {
                        const retries = overlapOptions.retries;

                        if (retries >= 0 && tryCount > retries) {
                            throw new Error("Particle is overlapping and can't be placed");
                        }

                        let overlaps = false;

                        for (const particle of this.container.particles.array) {
                            if (getDistance(pos, particle.position) < this.size.value + particle.size.value) {
                                overlaps = true;
                                break;
                            }
                        }

                        return overlaps;
                    }

                    return false;
                }

                calculateVelocity() {
                    const baseVelocity = getParticleBaseVelocity(this.direction);
                    const res = baseVelocity.copy();
                    const moveOptions = this.options.move;
                    const rad = Math.PI / 180 * moveOptions.angle.value;
                    const radOffset = Math.PI / 180 * moveOptions.angle.offset;
                    const range = {
                        left: radOffset - rad / 2,
                        right: radOffset + rad / 2
                    };

                    if (!moveOptions.straight) {
                        res.angle += randomInRange(setRangeValue(range.left, range.right));
                    }

                    if (moveOptions.random && typeof moveOptions.speed === "number") {
                        res.length *= Math.random();
                    }

                    return res;
                }

                loadShapeData(shapeOptions, reduceDuplicates) {
                    const shapeData = shapeOptions.options[this.shape];

                    if (shapeData) {
                        return deepExtend({}, shapeData instanceof Array ? itemFromArray(shapeData, this.id, reduceDuplicates) : shapeData);
                    }
                }

                loadLife() {
                    const container = this.container;
                    const particlesOptions = this.options;
                    const lifeOptions = particlesOptions.life;
                    const life = {
                        delay: container.retina.reduceFactor ? getRangeValue(lifeOptions.delay.value) * (lifeOptions.delay.sync ? 1 : Math.random()) / container.retina.reduceFactor * 1000 : 0,
                        delayTime: 0,
                        duration: container.retina.reduceFactor ? getRangeValue(lifeOptions.duration.value) * (lifeOptions.duration.sync ? 1 : Math.random()) / container.retina.reduceFactor * 1000 : 0,
                        time: 0,
                        count: particlesOptions.life.count
                    };

                    if (life.duration <= 0) {
                        life.duration = -1;
                    }

                    if (life.count <= 0) {
                        life.count = -1;
                    }

                    return life;
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Core/InteractionManager.js


            class InteractionManager {
                constructor(container) {
                    this.container = container;
                    const interactors = Plugins.getInteractors(container);
                    this.externalInteractors = [];
                    this.particleInteractors = [];

                    for (const interactor of interactors) {
                        switch (interactor.type) {
                            case InteractorType.External:
                                this.externalInteractors.push(interactor);
                                break;

                            case InteractorType.Particles:
                                this.particleInteractors.push(interactor);
                                break;
                        }
                    }
                }

                externalInteract(delta) {
                    for (const interactor of this.externalInteractors) {
                        if (interactor.isEnabled()) {
                            interactor.interact(delta);
                        }
                    }
                }

                particlesInteract(particle, delta) {
                    for (const interactor of this.externalInteractors) {
                        interactor.reset(particle);
                    }

                    for (const interactor of this.particleInteractors) {
                        if (interactor.isEnabled(particle)) {
                            interactor.interact(particle, delta);
                        }
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Core/Particle/Mover.js



            function applyDistance(particle) {
                const initialPosition = particle.initialPosition;
                const {
                    dx,
                    dy
                } = getDistances(initialPosition, particle.position);
                const dxFixed = Math.abs(dx),
                    dyFixed = Math.abs(dy);
                const hDistance = particle.maxDistance.horizontal;
                const vDistance = particle.maxDistance.vertical;

                if (!hDistance && !vDistance) {
                    return;
                }

                if ((hDistance && dxFixed >= hDistance || vDistance && dyFixed >= vDistance) && !particle.misplaced) {
                    particle.misplaced = !!hDistance && dxFixed > hDistance || !!vDistance && dyFixed > vDistance;

                    if (hDistance) {
                        particle.velocity.x = particle.velocity.y / 2 - particle.velocity.x;
                    }

                    if (vDistance) {
                        particle.velocity.y = particle.velocity.x / 2 - particle.velocity.y;
                    }
                } else if ((!hDistance || dxFixed < hDistance) && (!vDistance || dyFixed < vDistance) && particle.misplaced) {
                    particle.misplaced = false;
                } else if (particle.misplaced) {
                    const pos = particle.position,
                        vel = particle.velocity;

                    if (hDistance && (pos.x < initialPosition.x && vel.x < 0 || pos.x > initialPosition.x && vel.x > 0)) {
                        vel.x *= -Math.random();
                    }

                    if (vDistance && (pos.y < initialPosition.y && vel.y < 0 || pos.y > initialPosition.y && vel.y > 0)) {
                        vel.y *= -Math.random();
                    }
                }
            }

            class Mover {
                constructor(container) {
                    this.container = container;
                }

                move(particle, delta) {
                    if (particle.destroyed) {
                        return;
                    }

                    this.moveParticle(particle, delta);
                    this.moveParallax(particle);
                }

                moveParticle(particle, delta) {
                    var _a, _b, _c;

                    const particleOptions = particle.options;
                    const moveOptions = particleOptions.move;

                    if (!moveOptions.enable) {
                        return;
                    }

                    const container = this.container,
                        slowFactor = this.getProximitySpeedFactor(particle),
                        baseSpeed = ((_a = particle.moveSpeed) !== null && _a !== void 0 ? _a : particle.moveSpeed = getRangeValue(moveOptions.speed) * container.retina.pixelRatio) * container.retina.reduceFactor,
                        moveDrift = (_b = particle.moveDrift) !== null && _b !== void 0 ? _b : particle.moveDrift = getRangeValue(particle.options.move.drift) * container.retina.pixelRatio,
                        maxSize = getRangeMax(particleOptions.size.value) * container.retina.pixelRatio,
                        sizeFactor = moveOptions.size ? particle.getRadius() / maxSize : 1,
                        diffFactor = 2,
                        speedFactor = sizeFactor * slowFactor * delta.factor / diffFactor,
                        moveSpeed = baseSpeed * speedFactor;
                    this.applyPath(particle, delta);
                    const gravityOptions = moveOptions.gravity;
                    const gravityFactor = gravityOptions.enable && gravityOptions.inverse ? -1 : 1;

                    if (gravityOptions.enable) {
                        particle.velocity.y += gravityFactor * (gravityOptions.acceleration * delta.factor) / (60 * moveSpeed);
                    }

                    if (moveDrift && moveSpeed) {
                        particle.velocity.x += moveDrift * delta.factor / (60 * moveSpeed);
                    }

                    const decay = 1 - particle.options.move.decay;

                    if (decay != 1) {
                        particle.velocity.multTo(decay);
                    }

                    const velocity = particle.velocity.mult(moveSpeed);
                    const maxSpeed = (_c = particle.maxSpeed) !== null && _c !== void 0 ? _c : container.retina.maxSpeed;

                    if (gravityOptions.enable && gravityOptions.maxSpeed > 0 && (!gravityOptions.inverse && velocity.y >= 0 && velocity.y >= maxSpeed || gravityOptions.inverse && velocity.y <= 0 && velocity.y <= -maxSpeed)) {
                        velocity.y = gravityFactor * maxSpeed;

                        if (moveSpeed) {
                            particle.velocity.y = velocity.y / moveSpeed;
                        }
                    }

                    const zIndexOptions = particle.options.zIndex,
                        zVelocityFactor = 1 - zIndexOptions.velocityRate * particle.zIndexFactor;

                    if (moveOptions.spin.enable) {
                        this.spin(particle, moveSpeed);
                    } else {
                        if (zVelocityFactor != 1) {
                            velocity.multTo(zVelocityFactor);
                        }

                        particle.position.addTo(velocity);

                        if (moveOptions.vibrate) {
                            particle.position.x += Math.sin(particle.position.x * Math.cos(particle.position.y));
                            particle.position.y += Math.cos(particle.position.y * Math.sin(particle.position.x));
                        }
                    }

                    applyDistance(particle);
                }

                spin(particle, moveSpeed) {
                    const container = this.container;

                    if (!particle.spin) {
                        return;
                    }

                    const updateFunc = {
                        x: particle.spin.direction === RotateDirection.clockwise ? Math.cos : Math.sin,
                        y: particle.spin.direction === RotateDirection.clockwise ? Math.sin : Math.cos
                    };
                    particle.position.x = particle.spin.center.x + particle.spin.radius * updateFunc.x(particle.spin.angle);
                    particle.position.y = particle.spin.center.y + particle.spin.radius * updateFunc.y(particle.spin.angle);
                    particle.spin.radius += particle.spin.acceleration;
                    const maxCanvasSize = Math.min(container.canvas.size.width, container.canvas.size.height);

                    if (particle.spin.radius > maxCanvasSize / 2) {
                        particle.spin.radius = maxCanvasSize / 2;
                        particle.spin.acceleration *= -1;
                    } else if (particle.spin.radius < 0) {
                        particle.spin.radius = 0;
                        particle.spin.acceleration *= -1;
                    }

                    particle.spin.angle += moveSpeed / 100 * (1 - particle.spin.radius / maxCanvasSize);
                }

                applyPath(particle, delta) {
                    const particlesOptions = particle.options;
                    const pathOptions = particlesOptions.move.path;
                    const pathEnabled = pathOptions.enable;

                    if (!pathEnabled) {
                        return;
                    }

                    const container = this.container;

                    if (particle.lastPathTime <= particle.pathDelay) {
                        particle.lastPathTime += delta.value;
                        return;
                    }

                    let generator = container.pathGenerator;

                    if (pathOptions.generator) {
                        const customGenerator = Plugins.getPathGenerator(pathOptions.generator);

                        if (customGenerator) {
                            generator = customGenerator;
                        }
                    }

                    const path = generator.generate(particle);
                    particle.velocity.addTo(path);

                    if (pathOptions.clamp) {
                        particle.velocity.x = clamp(particle.velocity.x, -1, 1);
                        particle.velocity.y = clamp(particle.velocity.y, -1, 1);
                    }

                    particle.lastPathTime -= particle.pathDelay;
                }

                moveParallax(particle) {
                    const container = this.container;
                    const options = container.actualOptions;

                    if (isSsr() || !options.interactivity.events.onHover.parallax.enable) {
                        return;
                    }

                    const parallaxForce = options.interactivity.events.onHover.parallax.force;
                    const mousePos = container.interactivity.mouse.position;

                    if (!mousePos) {
                        return;
                    }

                    const canvasCenter = {
                        x: container.canvas.size.width / 2,
                        y: container.canvas.size.height / 2
                    };
                    const parallaxSmooth = options.interactivity.events.onHover.parallax.smooth;
                    const factor = particle.getRadius() / parallaxForce;
                    const tmp = {
                        x: (mousePos.x - canvasCenter.x) * factor,
                        y: (mousePos.y - canvasCenter.y) * factor
                    };
                    particle.offset.x += (tmp.x - particle.offset.x) / parallaxSmooth;
                    particle.offset.y += (tmp.y - particle.offset.y) / parallaxSmooth;
                }

                getProximitySpeedFactor(particle) {
                    const container = this.container;
                    const options = container.actualOptions;
                    const active = isInArray(HoverMode.slow, options.interactivity.events.onHover.mode);

                    if (!active) {
                        return 1;
                    }

                    const mousePos = this.container.interactivity.mouse.position;

                    if (!mousePos) {
                        return 1;
                    }

                    const particlePos = particle.getPosition();
                    const dist = getDistance(mousePos, particlePos);
                    const radius = container.retina.slowModeRadius;

                    if (dist > radius) {
                        return 1;
                    }

                    const proximityFactor = dist / radius || 0;
                    const slowFactor = options.interactivity.modes.slow.factor;
                    return proximityFactor / slowFactor;
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Core/Particles.js





            class Particles {
                constructor(container) {
                    this.container = container;
                    this.nextId = 0;
                    this.array = [];
                    this.zArray = [];
                    this.mover = new Mover(container);
                    this.limit = 0;
                    this.needsSort = false;
                    this.lastZIndex = 0;
                    this.linksFreq = new Map();
                    this.trianglesFreq = new Map();
                    this.interactionManager = new InteractionManager(container);
                    const canvasSize = this.container.canvas.size;
                    this.linksColors = new Map();
                    this.quadTree = new QuadTree(new Rectangle(-canvasSize.width / 4, -canvasSize.height / 4, canvasSize.width * 3 / 2, canvasSize.height * 3 / 2), 4);
                    this.updaters = Plugins.getUpdaters(container);
                }

                get count() {
                    return this.array.length;
                }

                init() {
                    var _a;

                    const container = this.container;
                    const options = container.actualOptions;
                    this.lastZIndex = 0;
                    this.needsSort = false;
                    this.linksFreq = new Map();
                    this.trianglesFreq = new Map();
                    let handled = false;

                    for (const [, plugin] of container.plugins) {
                        if (plugin.particlesInitialization !== undefined) {
                            handled = plugin.particlesInitialization();
                        }

                        if (handled) {
                            break;
                        }
                    }

                    this.addManualParticles();

                    if (!handled) {
                        for (const group in options.particles.groups) {
                            const groupOptions = options.particles.groups[group];

                            for (let i = this.count, j = 0; j < ((_a = groupOptions.number) === null || _a === void 0 ? void 0 : _a.value) && i < options.particles.number.value; i++, j++) {
                                this.addParticle(undefined, groupOptions, group);
                            }
                        }

                        for (let i = this.count; i < options.particles.number.value; i++) {
                            this.addParticle();
                        }
                    }

                    container.pathGenerator.init();
                }

                redraw() {
                    this.clear();
                    this.init();
                    this.draw({
                        value: 0,
                        factor: 0
                    });
                }

                removeAt(index, quantity = 1, group, override) {
                    if (!(index >= 0 && index <= this.count)) {
                        return;
                    }

                    let deleted = 0;

                    for (let i = index; deleted < quantity && i < this.count; i++) {
                        const particle = this.array[i];

                        if (!particle || particle.group !== group) {
                            continue;
                        }

                        particle.destroy(override);
                        this.array.splice(i--, 1);
                        const zIdx = this.zArray.indexOf(particle);
                        this.zArray.splice(zIdx, 1);
                        deleted++;
                    }
                }

                remove(particle, group, override) {
                    this.removeAt(this.array.indexOf(particle), undefined, group, override);
                }

                update(delta) {
                    const container = this.container;
                    const particlesToDelete = [];
                    container.pathGenerator.update();

                    for (const [, plugin] of container.plugins) {
                        if (plugin.update !== undefined) {
                            plugin.update(delta);
                        }
                    }

                    for (const particle of this.array) {
                        const resizeFactor = container.canvas.resizeFactor;

                        if (resizeFactor) {
                            particle.position.x *= resizeFactor.width;
                            particle.position.y *= resizeFactor.height;
                        }

                        particle.bubble.inRange = false;

                        for (const [, plugin] of this.container.plugins) {
                            if (particle.destroyed) {
                                break;
                            }

                            if (plugin.particleUpdate) {
                                plugin.particleUpdate(particle, delta);
                            }
                        }

                        this.mover.move(particle, delta);

                        if (particle.destroyed) {
                            particlesToDelete.push(particle);
                            continue;
                        }

                        this.quadTree.insert(new Point(particle.getPosition(), particle));
                    }

                    for (const particle of particlesToDelete) {
                        this.remove(particle);
                    }

                    this.interactionManager.externalInteract(delta);

                    for (const particle of container.particles.array) {
                        for (const updater of this.updaters) {
                            updater.update(particle, delta);
                        }

                        if (!particle.destroyed && !particle.spawning) {
                            this.interactionManager.particlesInteract(particle, delta);
                        }
                    }

                    delete container.canvas.resizeFactor;
                }

                draw(delta) {
                    const container = this.container;
                    container.canvas.clear();
                    const canvasSize = this.container.canvas.size;
                    this.quadTree = new QuadTree(new Rectangle(-canvasSize.width / 4, -canvasSize.height / 4, canvasSize.width * 3 / 2, canvasSize.height * 3 / 2), 4);
                    this.update(delta);

                    if (this.needsSort) {
                        this.zArray.sort((a, b) => b.position.z - a.position.z || a.id - b.id);
                        this.lastZIndex = this.zArray[this.zArray.length - 1].position.z;
                        this.needsSort = false;
                    }

                    for (const [, plugin] of container.plugins) {
                        container.canvas.drawPlugin(plugin, delta);
                    }

                    for (const p of this.zArray) {
                        p.draw(delta);
                    }
                }

                clear() {
                    this.array = [];
                    this.zArray = [];
                }

                push(nb, mouse, overrideOptions, group) {
                    this.pushing = true;

                    for (let i = 0; i < nb; i++) {
                        this.addParticle(mouse === null || mouse === void 0 ? void 0 : mouse.position, overrideOptions, group);
                    }

                    this.pushing = false;
                }

                addParticle(position, overrideOptions, group) {
                    const container = this.container;
                    const options = container.actualOptions;
                    const limit = options.particles.number.limit * container.density;

                    if (limit > 0) {
                        const countToRemove = this.count + 1 - limit;

                        if (countToRemove > 0) {
                            this.removeQuantity(countToRemove);
                        }
                    }

                    return this.pushParticle(position, overrideOptions, group);
                }

                addSplitParticle(parent) {
                    const splitOptions = parent.options.destroy.split;
                    const options = new ParticlesOptions();
                    options.load(parent.options);
                    const factor = getRangeValue(splitOptions.factor.value);
                    options.color.load({
                        value: {
                            hsl: parent.getFillColor()
                        }
                    });

                    if (typeof options.size.value === "number") {
                        options.size.value /= factor;
                    } else {
                        options.size.value.min /= factor;
                        options.size.value.max /= factor;
                    }

                    options.load(splitOptions.particles);
                    const offset = splitOptions.sizeOffset ? setRangeValue(-parent.size.value, parent.size.value) : 0;
                    const position = {
                        x: parent.position.x + randomInRange(offset),
                        y: parent.position.y + randomInRange(offset)
                    };
                    return this.pushParticle(position, options, parent.group, particle => {
                        if (particle.size.value < 0.5) {
                            return false;
                        }

                        particle.velocity.length = randomInRange(setRangeValue(parent.velocity.length, particle.velocity.length));
                        particle.splitCount = parent.splitCount + 1;
                        particle.unbreakable = true;
                        setTimeout(() => {
                            particle.unbreakable = false;
                        }, 500);
                        return true;
                    });
                }

                removeQuantity(quantity, group) {
                    this.removeAt(0, quantity, group);
                }

                getLinkFrequency(p1, p2) {
                    const key = `${Math.min(p1.id, p2.id)}_${Math.max(p1.id, p2.id)}`;
                    let res = this.linksFreq.get(key);

                    if (res === undefined) {
                        res = Math.random();
                        this.linksFreq.set(key, res);
                    }

                    return res;
                }

                getTriangleFrequency(p1, p2, p3) {
                    let [id1, id2, id3] = [p1.id, p2.id, p3.id];

                    if (id1 > id2) {
                        [id2, id1] = [id1, id2];
                    }

                    if (id2 > id3) {
                        [id3, id2] = [id2, id3];
                    }

                    if (id1 > id3) {
                        [id3, id1] = [id1, id3];
                    }

                    const key = `${id1}_${id2}_${id3}`;
                    let res = this.trianglesFreq.get(key);

                    if (res === undefined) {
                        res = Math.random();
                        this.trianglesFreq.set(key, res);
                    }

                    return res;
                }

                addManualParticles() {
                    const container = this.container;
                    const options = container.actualOptions;

                    for (const particle of options.manualParticles) {
                        const pos = particle.position ? {
                            x: particle.position.x * container.canvas.size.width / 100,
                            y: particle.position.y * container.canvas.size.height / 100
                        } : undefined;
                        this.addParticle(pos, particle.options);
                    }
                }

                setDensity() {
                    const options = this.container.actualOptions;

                    for (const group in options.particles.groups) {
                        this.applyDensity(options.particles.groups[group], 0, group);
                    }

                    this.applyDensity(options.particles, options.manualParticles.length);
                }

                applyDensity(options, manualCount, group) {
                    var _a;

                    if (!((_a = options.number.density) === null || _a === void 0 ? void 0 : _a.enable)) {
                        return;
                    }

                    const numberOptions = options.number;
                    const densityFactor = this.initDensityFactor(numberOptions.density);
                    const optParticlesNumber = numberOptions.value;
                    const optParticlesLimit = numberOptions.limit > 0 ? numberOptions.limit : optParticlesNumber;
                    const particlesNumber = Math.min(optParticlesNumber, optParticlesLimit) * densityFactor + manualCount;
                    const particlesCount = Math.min(this.count, this.array.filter(t => t.group === group).length);
                    this.limit = numberOptions.limit * densityFactor;

                    if (particlesCount < particlesNumber) {
                        this.push(Math.abs(particlesNumber - particlesCount), undefined, options, group);
                    } else if (particlesCount > particlesNumber) {
                        this.removeQuantity(particlesCount - particlesNumber, group);
                    }
                }

                initDensityFactor(densityOptions) {
                    const container = this.container;

                    if (!container.canvas.element || !densityOptions.enable) {
                        return 1;
                    }

                    const canvas = container.canvas.element;
                    const pxRatio = container.retina.pixelRatio;
                    return canvas.width * canvas.height / (densityOptions.factor * Math.pow(pxRatio, 2) * densityOptions.area);
                }

                pushParticle(position, overrideOptions, group, initializer) {
                    try {
                        const particle = new Particle(this.nextId, this.container, position, overrideOptions, group);
                        let canAdd = true;

                        if (initializer) {
                            canAdd = initializer(particle);
                        }

                        if (!canAdd) {
                            return;
                        }

                        this.array.push(particle);
                        this.zArray.push(particle);
                        this.nextId++;
                        return particle;
                    } catch (e) {
                        console.warn(`error adding particle: ${e}`);
                        return;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Core/Retina.js

            class Retina {
                constructor(container) {
                    this.container = container;
                }

                init() {
                    const container = this.container;
                    const options = container.actualOptions;
                    this.pixelRatio = !options.detectRetina || isSsr() ? 1 : window.devicePixelRatio;
                    const motionOptions = this.container.actualOptions.motion;

                    if (motionOptions && (motionOptions.disable || motionOptions.reduce.value)) {
                        if (isSsr() || typeof matchMedia === "undefined" || !matchMedia) {
                            this.reduceFactor = 1;
                        } else {
                            const mediaQuery = matchMedia("(prefers-reduced-motion: reduce)");

                            if (mediaQuery) {
                                this.handleMotionChange(mediaQuery);

                                const handleChange = () => {
                                    this.handleMotionChange(mediaQuery);
                                    container.refresh().catch(() => { });
                                };

                                if (mediaQuery.addEventListener !== undefined) {
                                    mediaQuery.addEventListener("change", handleChange);
                                } else if (mediaQuery.addListener !== undefined) {
                                    mediaQuery.addListener(handleChange);
                                }
                            }
                        }
                    } else {
                        this.reduceFactor = 1;
                    }

                    const ratio = this.pixelRatio;

                    if (container.canvas.element) {
                        const element = container.canvas.element;
                        container.canvas.size.width = element.offsetWidth * ratio;
                        container.canvas.size.height = element.offsetHeight * ratio;
                    }

                    const particles = options.particles;
                    this.attractDistance = particles.move.attract.distance * ratio;
                    this.linksDistance = particles.links.distance * ratio;
                    this.linksWidth = particles.links.width * ratio;
                    this.sizeAnimationSpeed = particles.size.animation.speed * ratio;
                    this.maxSpeed = particles.move.gravity.maxSpeed * ratio;

                    if (particles.orbit.radius !== undefined) {
                        this.orbitRadius = particles.orbit.radius * this.container.retina.pixelRatio;
                    }

                    const modes = options.interactivity.modes;
                    this.connectModeDistance = modes.connect.distance * ratio;
                    this.connectModeRadius = modes.connect.radius * ratio;
                    this.grabModeDistance = modes.grab.distance * ratio;
                    this.repulseModeDistance = modes.repulse.distance * ratio;
                    this.bounceModeDistance = modes.bounce.distance * ratio;
                    this.attractModeDistance = modes.attract.distance * ratio;
                    this.slowModeRadius = modes.slow.radius * ratio;
                    this.bubbleModeDistance = modes.bubble.distance * ratio;

                    if (modes.bubble.size) {
                        this.bubbleModeSize = modes.bubble.size * ratio;
                    }
                }

                initParticle(particle) {
                    const options = particle.options;
                    const ratio = this.pixelRatio;
                    const moveDistance = options.move.distance;
                    const orbit = options.orbit;
                    particle.attractDistance = options.move.attract.distance * ratio;
                    particle.linksDistance = options.links.distance * ratio;
                    particle.linksWidth = options.links.width * ratio;
                    particle.moveDrift = getRangeValue(options.move.drift) * ratio;
                    particle.moveSpeed = getRangeValue(options.move.speed) * ratio;
                    particle.sizeAnimationSpeed = options.size.animation.speed * ratio;
                    particle.orbitRadius = (orbit === null || orbit === void 0 ? void 0 : orbit.radius) !== undefined ? orbit.radius * ratio : undefined;

                    if (particle.spin) {
                        particle.spin.acceleration = getRangeValue(options.move.spin.acceleration) * ratio;
                    }

                    const maxDistance = particle.maxDistance;
                    maxDistance.horizontal = moveDistance.horizontal !== undefined ? moveDistance.horizontal * ratio : undefined;
                    maxDistance.vertical = moveDistance.vertical !== undefined ? moveDistance.vertical * ratio : undefined;
                    particle.wobbleDistance = getRangeValue(options.wobble.distance) * ratio;
                    particle.maxSpeed = options.move.gravity.maxSpeed * ratio;
                }

                handleMotionChange(mediaQuery) {
                    const options = this.container.actualOptions;

                    if (mediaQuery.matches) {
                        const motion = options.motion;
                        this.reduceFactor = motion.disable ? 0 : motion.reduce.value ? 1 / motion.reduce.factor : 1;
                    } else {
                        this.reduceFactor = 1;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Core/FrameManager.js
            class FrameManager {
                constructor(container) {
                    this.container = container;
                }

                nextFrame(timestamp) {
                    var _a;

                    try {
                        const container = this.container;

                        if (container.lastFrameTime !== undefined && timestamp < container.lastFrameTime + 1000 / container.fpsLimit) {
                            container.draw(false);
                            return;
                        }

                        (_a = container.lastFrameTime) !== null && _a !== void 0 ? _a : container.lastFrameTime = timestamp;
                        const deltaValue = timestamp - container.lastFrameTime;
                        const delta = {
                            value: deltaValue,
                            factor: 60 * deltaValue / 1000
                        };
                        container.lifeTime += delta.value;
                        container.lastFrameTime = timestamp;

                        if (deltaValue > 1000) {
                            container.draw(false);
                            return;
                        }

                        container.particles.draw(delta);

                        if (container.duration > 0 && container.lifeTime > container.duration) {
                            container.destroy();
                            return;
                        }

                        if (container.getAnimationStatus()) {
                            container.draw(false);
                        }
                    } catch (e) {
                        console.error("tsParticles error in animation loop", e);
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Interactivity/Events/ClickEvent.js
            class ClickEvent {
                constructor() {
                    this.enable = false;
                    this.mode = [];
                }

                load(data) {
                    if (data === undefined) {
                        return;
                    }

                    if (data.enable !== undefined) {
                        this.enable = data.enable;
                    }

                    if (data.mode !== undefined) {
                        this.mode = data.mode;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Interactivity/Events/DivEvent.js

            class DivEvent {
                constructor() {
                    this.selectors = [];
                    this.enable = false;
                    this.mode = [];
                    this.type = DivType.circle;
                }

                get elementId() {
                    return this.ids;
                }

                set elementId(value) {
                    this.ids = value;
                }

                get el() {
                    return this.elementId;
                }

                set el(value) {
                    this.elementId = value;
                }

                get ids() {
                    return this.selectors instanceof Array ? this.selectors.map(t => t.replace("#", "")) : this.selectors.replace("#", "");
                }

                set ids(value) {
                    this.selectors = value instanceof Array ? value.map(t => `#${t}`) : `#${value}`;
                }

                load(data) {
                    var _a, _b;

                    if (data === undefined) {
                        return;
                    }

                    const ids = (_b = (_a = data.ids) !== null && _a !== void 0 ? _a : data.elementId) !== null && _b !== void 0 ? _b : data.el;

                    if (ids !== undefined) {
                        this.ids = ids;
                    }

                    if (data.selectors !== undefined) {
                        this.selectors = data.selectors;
                    }

                    if (data.enable !== undefined) {
                        this.enable = data.enable;
                    }

                    if (data.mode !== undefined) {
                        this.mode = data.mode;
                    }

                    if (data.type !== undefined) {
                        this.type = data.type;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Interactivity/Events/Parallax.js
            class Parallax {
                constructor() {
                    this.enable = false;
                    this.force = 2;
                    this.smooth = 10;
                }

                load(data) {
                    if (data === undefined) {
                        return;
                    }

                    if (data.enable !== undefined) {
                        this.enable = data.enable;
                    }

                    if (data.force !== undefined) {
                        this.force = data.force;
                    }

                    if (data.smooth !== undefined) {
                        this.smooth = data.smooth;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Interactivity/Events/HoverEvent.js

            class HoverEvent {
                constructor() {
                    this.enable = false;
                    this.mode = [];
                    this.parallax = new Parallax();
                }

                load(data) {
                    if (data === undefined) {
                        return;
                    }

                    if (data.enable !== undefined) {
                        this.enable = data.enable;
                    }

                    if (data.mode !== undefined) {
                        this.mode = data.mode;
                    }

                    this.parallax.load(data.parallax);
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Interactivity/Events/Events.js



            class Events {
                constructor() {
                    this.onClick = new ClickEvent();
                    this.onDiv = new DivEvent();
                    this.onHover = new HoverEvent();
                    this.resize = true;
                }

                get onclick() {
                    return this.onClick;
                }

                set onclick(value) {
                    this.onClick = value;
                }

                get ondiv() {
                    return this.onDiv;
                }

                set ondiv(value) {
                    this.onDiv = value;
                }

                get onhover() {
                    return this.onHover;
                }

                set onhover(value) {
                    this.onHover = value;
                }

                load(data) {
                    var _a, _b, _c;

                    if (data === undefined) {
                        return;
                    }

                    this.onClick.load((_a = data.onClick) !== null && _a !== void 0 ? _a : data.onclick);
                    const onDiv = (_b = data.onDiv) !== null && _b !== void 0 ? _b : data.ondiv;

                    if (onDiv !== undefined) {
                        if (onDiv instanceof Array) {
                            this.onDiv = onDiv.map(div => {
                                const tmp = new DivEvent();
                                tmp.load(div);
                                return tmp;
                            });
                        } else {
                            this.onDiv = new DivEvent();
                            this.onDiv.load(onDiv);
                        }
                    }

                    this.onHover.load((_c = data.onHover) !== null && _c !== void 0 ? _c : data.onhover);

                    if (data.resize !== undefined) {
                        this.resize = data.resize;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Interactivity/Modes/BubbleBase.js

            class BubbleBase {
                constructor() {
                    this.distance = 200;
                    this.duration = 0.4;
                    this.mix = false;
                }

                load(data) {
                    if (data === undefined) {
                        return;
                    }

                    if (data.distance !== undefined) {
                        this.distance = data.distance;
                    }

                    if (data.duration !== undefined) {
                        this.duration = data.duration;
                    }

                    if (data.mix !== undefined) {
                        this.mix = data.mix;
                    }

                    if (data.opacity !== undefined) {
                        this.opacity = data.opacity;
                    }

                    if (data.color !== undefined) {
                        if (data.color instanceof Array) {
                            this.color = data.color.map(s => OptionsColor.create(undefined, s));
                        } else {
                            if (this.color instanceof Array) {
                                this.color = new OptionsColor();
                            }

                            this.color = OptionsColor.create(this.color, data.color);
                        }
                    }

                    if (data.size !== undefined) {
                        this.size = data.size;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Interactivity/Modes/BubbleDiv.js

            class BubbleDiv extends BubbleBase {
                constructor() {
                    super();
                    this.selectors = [];
                }

                get ids() {
                    return this.selectors instanceof Array ? this.selectors.map(t => t.replace("#", "")) : this.selectors.replace("#", "");
                }

                set ids(value) {
                    this.selectors = value instanceof Array ? value.map(t => `#${t}`) : `#${value}`;
                }

                load(data) {
                    super.load(data);

                    if (data === undefined) {
                        return;
                    }

                    if (data.ids !== undefined) {
                        this.ids = data.ids;
                    }

                    if (data.selectors !== undefined) {
                        this.selectors = data.selectors;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Interactivity/Modes/Bubble.js


            class Bubble extends BubbleBase {
                load(data) {
                    super.load(data);

                    if (!(data !== undefined && data.divs !== undefined)) {
                        return;
                    }

                    if (data.divs instanceof Array) {
                        this.divs = data.divs.map(s => {
                            const tmp = new BubbleDiv();
                            tmp.load(s);
                            return tmp;
                        });
                    } else {
                        if (this.divs instanceof Array || !this.divs) {
                            this.divs = new BubbleDiv();
                        }

                        this.divs.load(data.divs);
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Interactivity/Modes/ConnectLinks.js
            class ConnectLinks {
                constructor() {
                    this.opacity = 0.5;
                }

                load(data) {
                    if (!(data !== undefined && data.opacity !== undefined)) {
                        return;
                    }

                    this.opacity = data.opacity;
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Interactivity/Modes/Connect.js

            class Connect {
                constructor() {
                    this.distance = 80;
                    this.links = new ConnectLinks();
                    this.radius = 60;
                }

                get line_linked() {
                    return this.links;
                }

                set line_linked(value) {
                    this.links = value;
                }

                get lineLinked() {
                    return this.links;
                }

                set lineLinked(value) {
                    this.links = value;
                }

                load(data) {
                    var _a, _b;

                    if (data === undefined) {
                        return;
                    }

                    if (data.distance !== undefined) {
                        this.distance = data.distance;
                    }

                    this.links.load((_b = (_a = data.links) !== null && _a !== void 0 ? _a : data.lineLinked) !== null && _b !== void 0 ? _b : data.line_linked);

                    if (data.radius !== undefined) {
                        this.radius = data.radius;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Interactivity/Modes/GrabLinks.js

            class GrabLinks {
                constructor() {
                    this.blink = false;
                    this.consent = false;
                    this.opacity = 1;
                }

                load(data) {
                    if (data === undefined) {
                        return;
                    }

                    if (data.blink !== undefined) {
                        this.blink = data.blink;
                    }

                    if (data.color !== undefined) {
                        this.color = OptionsColor.create(this.color, data.color);
                    }

                    if (data.consent !== undefined) {
                        this.consent = data.consent;
                    }

                    if (data.opacity !== undefined) {
                        this.opacity = data.opacity;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Interactivity/Modes/Grab.js

            class Grab {
                constructor() {
                    this.distance = 100;
                    this.links = new GrabLinks();
                }

                get line_linked() {
                    return this.links;
                }

                set line_linked(value) {
                    this.links = value;
                }

                get lineLinked() {
                    return this.links;
                }

                set lineLinked(value) {
                    this.links = value;
                }

                load(data) {
                    var _a, _b;

                    if (data === undefined) {
                        return;
                    }

                    if (data.distance !== undefined) {
                        this.distance = data.distance;
                    }

                    this.links.load((_b = (_a = data.links) !== null && _a !== void 0 ? _a : data.lineLinked) !== null && _b !== void 0 ? _b : data.line_linked);
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Interactivity/Modes/Remove.js
            class Remove {
                constructor() {
                    this.quantity = 2;
                }

                get particles_nb() {
                    return this.quantity;
                }

                set particles_nb(value) {
                    this.quantity = value;
                }

                load(data) {
                    var _a;

                    if (data === undefined) {
                        return;
                    }

                    const quantity = (_a = data.quantity) !== null && _a !== void 0 ? _a : data.particles_nb;

                    if (quantity !== undefined) {
                        this.quantity = quantity;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Interactivity/Modes/Push.js
            class Push {
                constructor() {
                    this.default = true;
                    this.groups = [];
                    this.quantity = 4;
                }

                get particles_nb() {
                    return this.quantity;
                }

                set particles_nb(value) {
                    this.quantity = value;
                }

                load(data) {
                    var _a;

                    if (data === undefined) {
                        return;
                    }

                    if (data.default !== undefined) {
                        this.default = data.default;
                    }

                    if (data.groups !== undefined) {
                        this.groups = data.groups.map(t => t);
                    }

                    if (!this.groups.length) {
                        this.default = true;
                    }

                    const quantity = (_a = data.quantity) !== null && _a !== void 0 ? _a : data.particles_nb;

                    if (quantity !== undefined) {
                        this.quantity = quantity;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Interactivity/Modes/RepulseBase.js

            class RepulseBase {
                constructor() {
                    this.distance = 200;
                    this.duration = 0.4;
                    this.factor = 100;
                    this.speed = 1;
                    this.maxSpeed = 50;
                    this.easing = EasingType.easeOutQuad;
                }

                load(data) {
                    if (!data) {
                        return;
                    }

                    if (data.distance !== undefined) {
                        this.distance = data.distance;
                    }

                    if (data.duration !== undefined) {
                        this.duration = data.duration;
                    }

                    if (data.easing !== undefined) {
                        this.easing = data.easing;
                    }

                    if (data.factor !== undefined) {
                        this.factor = data.factor;
                    }

                    if (data.speed !== undefined) {
                        this.speed = data.speed;
                    }

                    if (data.maxSpeed !== undefined) {
                        this.maxSpeed = data.maxSpeed;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Interactivity/Modes/RepulseDiv.js

            class RepulseDiv extends RepulseBase {
                constructor() {
                    super();
                    this.selectors = [];
                }

                get ids() {
                    if (this.selectors instanceof Array) {
                        return this.selectors.map(t => t.replace("#", ""));
                    } else {
                        return this.selectors.replace("#", "");
                    }
                }

                set ids(value) {
                    if (value instanceof Array) {
                        this.selectors = value.map(() => `#${value}`);
                    } else {
                        this.selectors = `#${value}`;
                    }
                }

                load(data) {
                    super.load(data);

                    if (data === undefined) {
                        return;
                    }

                    if (data.ids !== undefined) {
                        this.ids = data.ids;
                    }

                    if (data.selectors !== undefined) {
                        this.selectors = data.selectors;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Interactivity/Modes/Repulse.js


            class Repulse_Repulse extends RepulseBase {
                load(data) {
                    super.load(data);

                    if ((data === null || data === void 0 ? void 0 : data.divs) === undefined) {
                        return;
                    }

                    if (data.divs instanceof Array) {
                        this.divs = data.divs.map(s => {
                            const tmp = new RepulseDiv();
                            tmp.load(s);
                            return tmp;
                        });
                    } else {
                        if (this.divs instanceof Array || !this.divs) {
                            this.divs = new RepulseDiv();
                        }

                        this.divs.load(data.divs);
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Interactivity/Modes/Slow.js
            class Slow {
                constructor() {
                    this.factor = 3;
                    this.radius = 200;
                }

                get active() {
                    return false;
                }

                set active(_value) { }

                load(data) {
                    if (data === undefined) {
                        return;
                    }

                    if (data.factor !== undefined) {
                        this.factor = data.factor;
                    }

                    if (data.radius !== undefined) {
                        this.radius = data.radius;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Interactivity/Modes/Trail.js

            class Trail_Trail {
                constructor() {
                    this.delay = 1;
                    this.pauseOnStop = false;
                    this.quantity = 1;
                }

                load(data) {
                    if (data === undefined) {
                        return;
                    }

                    if (data.delay !== undefined) {
                        this.delay = data.delay;
                    }

                    if (data.quantity !== undefined) {
                        this.quantity = data.quantity;
                    }

                    if (data.particles !== undefined) {
                        this.particles = deepExtend({}, data.particles);
                    }

                    if (data.pauseOnStop !== undefined) {
                        this.pauseOnStop = data.pauseOnStop;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Interactivity/Modes/Attract.js

            class Attract_Attract {
                constructor() {
                    this.distance = 200;
                    this.duration = 0.4;
                    this.easing = EasingType.easeOutQuad;
                    this.factor = 1;
                    this.maxSpeed = 50;
                    this.speed = 1;
                }

                load(data) {
                    if (!data) {
                        return;
                    }

                    if (data.distance !== undefined) {
                        this.distance = data.distance;
                    }

                    if (data.duration !== undefined) {
                        this.duration = data.duration;
                    }

                    if (data.easing !== undefined) {
                        this.easing = data.easing;
                    }

                    if (data.factor !== undefined) {
                        this.factor = data.factor;
                    }

                    if (data.maxSpeed !== undefined) {
                        this.maxSpeed = data.maxSpeed;
                    }

                    if (data.speed !== undefined) {
                        this.speed = data.speed;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Interactivity/Modes/LightGradient.js

            class LightGradient {
                constructor() {
                    this.start = new OptionsColor();
                    this.stop = new OptionsColor();
                    this.start.value = "#ffffff";
                    this.stop.value = "#000000";
                }

                load(data) {
                    if (data === undefined) {
                        return;
                    }

                    this.start = OptionsColor.create(this.start, data.start);
                    this.stop = OptionsColor.create(this.stop, data.stop);
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Interactivity/Modes/LightArea.js

            class LightArea {
                constructor() {
                    this.gradient = new LightGradient();
                    this.radius = 1000;
                }

                load(data) {
                    if (data === undefined) {
                        return;
                    }

                    this.gradient.load(data.gradient);

                    if (data.radius !== undefined) {
                        this.radius = data.radius;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Interactivity/Modes/LightShadow.js

            class LightShadow {
                constructor() {
                    this.color = new OptionsColor();
                    this.color.value = "#000000";
                    this.length = 2000;
                }

                load(data) {
                    if (data === undefined) {
                        return;
                    }

                    this.color = OptionsColor.create(this.color, data.color);

                    if (data.length !== undefined) {
                        this.length = data.length;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Interactivity/Modes/Light.js


            class Light {
                constructor() {
                    this.area = new LightArea();
                    this.shadow = new LightShadow();
                }

                load(data) {
                    if (data === undefined) {
                        return;
                    }

                    this.area.load(data.area);
                    this.shadow.load(data.shadow);
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Interactivity/Modes/Bounce.js
            class Bounce_Bounce {
                constructor() {
                    this.distance = 200;
                }

                load(data) {
                    if (!data) {
                        return;
                    }

                    if (data.distance !== undefined) {
                        this.distance = data.distance;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Interactivity/Modes/Modes.js











            class Modes {
                constructor() {
                    this.attract = new Attract_Attract();
                    this.bounce = new Bounce_Bounce();
                    this.bubble = new Bubble();
                    this.connect = new Connect();
                    this.grab = new Grab();
                    this.light = new Light();
                    this.push = new Push();
                    this.remove = new Remove();
                    this.repulse = new Repulse_Repulse();
                    this.slow = new Slow();
                    this.trail = new Trail_Trail();
                }

                load(data) {
                    if (data === undefined) {
                        return;
                    }

                    this.attract.load(data.attract);
                    this.bubble.load(data.bubble);
                    this.connect.load(data.connect);
                    this.grab.load(data.grab);
                    this.light.load(data.light);
                    this.push.load(data.push);
                    this.remove.load(data.remove);
                    this.repulse.load(data.repulse);
                    this.slow.load(data.slow);
                    this.trail.load(data.trail);
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Interactivity/Interactivity.js



            class Interactivity {
                constructor() {
                    this.detectsOn = InteractivityDetect.canvas;
                    this.events = new Events();
                    this.modes = new Modes();
                }

                get detect_on() {
                    return this.detectsOn;
                }

                set detect_on(value) {
                    this.detectsOn = value;
                }

                load(data) {
                    var _a, _b, _c;

                    if (data === undefined) {
                        return;
                    }

                    const detectsOn = (_a = data.detectsOn) !== null && _a !== void 0 ? _a : data.detect_on;

                    if (detectsOn !== undefined) {
                        this.detectsOn = detectsOn;
                    }

                    this.events.load(data.events);
                    this.modes.load(data.modes);

                    if (((_c = (_b = data.modes) === null || _b === void 0 ? void 0 : _b.slow) === null || _c === void 0 ? void 0 : _c.active) === true) {
                        if (this.events.onHover.mode instanceof Array) {
                            if (this.events.onHover.mode.indexOf(HoverMode.slow) < 0) {
                                this.events.onHover.mode.push(HoverMode.slow);
                            }
                        } else if (this.events.onHover.mode !== HoverMode.slow) {
                            this.events.onHover.mode = [this.events.onHover.mode, HoverMode.slow];
                        }
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/BackgroundMask/BackgroundMaskCover.js

            class BackgroundMaskCover {
                constructor() {
                    this.color = new OptionsColor();
                    this.opacity = 1;
                }

                load(data) {
                    if (data === undefined) {
                        return;
                    }

                    if (data.color !== undefined) {
                        this.color = OptionsColor.create(this.color, data.color);
                    }

                    if (data.opacity !== undefined) {
                        this.opacity = data.opacity;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/BackgroundMask/BackgroundMask.js

            class BackgroundMask {
                constructor() {
                    this.composite = "destination-out";
                    this.cover = new BackgroundMaskCover();
                    this.enable = false;
                }

                load(data) {
                    if (data === undefined) {
                        return;
                    }

                    if (data.composite !== undefined) {
                        this.composite = data.composite;
                    }

                    if (data.cover !== undefined) {
                        const cover = data.cover;
                        const color = typeof data.cover === "string" ? {
                            color: data.cover
                        } : data.cover;
                        this.cover.load(cover.color !== undefined ? cover : {
                            color: color
                        });
                    }

                    if (data.enable !== undefined) {
                        this.enable = data.enable;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Background/Background.js

            class Background {
                constructor() {
                    this.color = new OptionsColor();
                    this.color.value = "";
                    this.image = "";
                    this.position = "";
                    this.repeat = "";
                    this.size = "";
                    this.opacity = 1;
                }

                load(data) {
                    if (data === undefined) {
                        return;
                    }

                    if (data.color !== undefined) {
                        this.color = OptionsColor.create(this.color, data.color);
                    }

                    if (data.image !== undefined) {
                        this.image = data.image;
                    }

                    if (data.position !== undefined) {
                        this.position = data.position;
                    }

                    if (data.repeat !== undefined) {
                        this.repeat = data.repeat;
                    }

                    if (data.size !== undefined) {
                        this.size = data.size;
                    }

                    if (data.opacity !== undefined) {
                        this.opacity = data.opacity;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Theme/ThemeDefault.js

            class ThemeDefault {
                constructor() {
                    this.mode = ThemeMode.any;
                    this.value = false;
                }

                load(data) {
                    if (data === undefined) {
                        return;
                    }

                    if (data.mode !== undefined) {
                        this.mode = data.mode;
                    }

                    if (data.value !== undefined) {
                        this.value = data.value;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Theme/Theme.js


            class Theme {
                constructor() {
                    this.name = "";
                    this.default = new ThemeDefault();
                }

                load(data) {
                    if (data === undefined) {
                        return;
                    }

                    if (data.name !== undefined) {
                        this.name = data.name;
                    }

                    this.default.load(data.default);

                    if (data.options !== undefined) {
                        this.options = deepExtend({}, data.options);
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/FullScreen/FullScreen.js
            class FullScreen {
                constructor() {
                    this.enable = false;
                    this.zIndex = -1;
                }

                load(data) {
                    if (!data) {
                        return;
                    }

                    if (data.enable !== undefined) {
                        this.enable = data.enable;
                    }

                    if (data.zIndex !== undefined) {
                        this.zIndex = data.zIndex;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Motion/MotionReduce.js
            class MotionReduce {
                constructor() {
                    this.factor = 4;
                    this.value = true;
                }

                load(data) {
                    if (!data) {
                        return;
                    }

                    if (data.factor !== undefined) {
                        this.factor = data.factor;
                    }

                    if (data.value !== undefined) {
                        this.value = data.value;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Motion/Motion.js

            class Motion {
                constructor() {
                    this.disable = false;
                    this.reduce = new MotionReduce();
                }

                load(data) {
                    if (!data) {
                        return;
                    }

                    if (data.disable !== undefined) {
                        this.disable = data.disable;
                    }

                    this.reduce.load(data.reduce);
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/ManualParticle.js

            class ManualParticle {
                load(data) {
                    var _a, _b;

                    if (!data) {
                        return;
                    }

                    if (data.position !== undefined) {
                        this.position = {
                            x: (_a = data.position.x) !== null && _a !== void 0 ? _a : 50,
                            y: (_b = data.position.y) !== null && _b !== void 0 ? _b : 50
                        };
                    }

                    if (data.options !== undefined) {
                        this.options = deepExtend({}, data.options);
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Responsive.js

            class Responsive {
                constructor() {
                    this.maxWidth = Infinity;
                    this.options = {};
                }

                load(data) {
                    if (!data) {
                        return;
                    }

                    if (data.maxWidth !== undefined) {
                        this.maxWidth = data.maxWidth;
                    }

                    if (data.options !== undefined) {
                        this.options = deepExtend({}, data.options);
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Options/Classes/Options.js











            class Options {
                constructor() {
                    this.autoPlay = true;
                    this.background = new Background();
                    this.backgroundMask = new BackgroundMask();
                    this.fullScreen = new FullScreen();
                    this.detectRetina = true;
                    this.duration = 0;
                    this.fpsLimit = 60;
                    this.interactivity = new Interactivity();
                    this.manualParticles = [];
                    this.motion = new Motion();
                    this.particles = new ParticlesOptions();
                    this.pauseOnBlur = true;
                    this.pauseOnOutsideViewport = true;
                    this.responsive = [];
                    this.themes = [];
                }

                get fps_limit() {
                    return this.fpsLimit;
                }

                set fps_limit(value) {
                    this.fpsLimit = value;
                }

                get retina_detect() {
                    return this.detectRetina;
                }

                set retina_detect(value) {
                    this.detectRetina = value;
                }

                get backgroundMode() {
                    return this.fullScreen;
                }

                set backgroundMode(value) {
                    this.fullScreen.load(value);
                }

                load(data) {
                    var _a, _b, _c;

                    if (data === undefined) {
                        return;
                    }

                    if (data.preset !== undefined) {
                        if (data.preset instanceof Array) {
                            for (const preset of data.preset) {
                                this.importPreset(preset);
                            }
                        } else {
                            this.importPreset(data.preset);
                        }
                    }

                    if (data.autoPlay !== undefined) {
                        this.autoPlay = data.autoPlay;
                    }

                    const detectRetina = (_a = data.detectRetina) !== null && _a !== void 0 ? _a : data.retina_detect;

                    if (detectRetina !== undefined) {
                        this.detectRetina = detectRetina;
                    }

                    if (data.duration !== undefined) {
                        this.duration = data.duration;
                    }

                    const fpsLimit = (_b = data.fpsLimit) !== null && _b !== void 0 ? _b : data.fps_limit;

                    if (fpsLimit !== undefined) {
                        this.fpsLimit = fpsLimit;
                    }

                    if (data.pauseOnBlur !== undefined) {
                        this.pauseOnBlur = data.pauseOnBlur;
                    }

                    if (data.pauseOnOutsideViewport !== undefined) {
                        this.pauseOnOutsideViewport = data.pauseOnOutsideViewport;
                    }

                    this.background.load(data.background);
                    this.fullScreen.load((_c = data.fullScreen) !== null && _c !== void 0 ? _c : data.backgroundMode);
                    this.backgroundMask.load(data.backgroundMask);
                    this.interactivity.load(data.interactivity);

                    if (data.manualParticles !== undefined) {
                        this.manualParticles = data.manualParticles.map(t => {
                            const tmp = new ManualParticle();
                            tmp.load(t);
                            return tmp;
                        });
                    }

                    this.motion.load(data.motion);
                    this.particles.load(data.particles);
                    Plugins.loadOptions(this, data);

                    if (data.responsive !== undefined) {
                        for (const responsive of data.responsive) {
                            const optResponsive = new Responsive();
                            optResponsive.load(responsive);
                            this.responsive.push(optResponsive);
                        }
                    }

                    this.responsive.sort((a, b) => a.maxWidth - b.maxWidth);

                    if (data.themes !== undefined) {
                        for (const theme of data.themes) {
                            const optTheme = new Theme();
                            optTheme.load(theme);
                            this.themes.push(optTheme);
                        }
                    }
                }

                setTheme(name) {
                    if (name) {
                        const chosenTheme = this.themes.find(theme => theme.name === name);

                        if (chosenTheme) {
                            this.load(chosenTheme.options);
                        }
                    } else {
                        const clientDarkMode = typeof matchMedia !== "undefined" && matchMedia("(prefers-color-scheme: dark)").matches;
                        let defaultTheme = this.themes.find(theme => theme.default.value && (theme.default.mode === ThemeMode.dark && clientDarkMode || theme.default.mode === ThemeMode.light && !clientDarkMode));

                        if (!defaultTheme) {
                            defaultTheme = this.themes.find(theme => theme.default.value && theme.default.mode === ThemeMode.any);
                        }

                        if (defaultTheme) {
                            this.load(defaultTheme.options);
                        }
                    }
                }

                importPreset(preset) {
                    this.load(Plugins.getPreset(preset));
                }

                setResponsive(width, pxRatio, defaultOptions) {
                    var _a;

                    this.load(defaultOptions);
                    this.load((_a = this.responsive.find(t => t.maxWidth * pxRatio > width)) === null || _a === void 0 ? void 0 : _a.options);
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Core/Container.js
            var Container_awaiter = undefined && undefined.__awaiter || function (thisArg, _arguments, P, generator) {
                function adopt(value) {
                    return value instanceof P ? value : new P(function (resolve) {
                        resolve(value);
                    });
                }

                return new (P || (P = Promise))(function (resolve, reject) {
                    function fulfilled(value) {
                        try {
                            step(generator.next(value));
                        } catch (e) {
                            reject(e);
                        }
                    }

                    function rejected(value) {
                        try {
                            step(generator["throw"](value));
                        } catch (e) {
                            reject(e);
                        }
                    }

                    function step(result) {
                        result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected);
                    }

                    step((generator = generator.apply(thisArg, _arguments || [])).next());
                });
            };








            class Container {
                constructor(id, sourceOptions, ...presets) {
                    this.id = id;
                    this.zLayers = 10000;
                    this.fpsLimit = 60;
                    this.duration = 0;
                    this.lifeTime = 0;
                    this.firstStart = true;
                    this.started = false;
                    this.destroyed = false;
                    this.paused = true;
                    this.lastFrameTime = 0;
                    this.pageHidden = false;
                    this._sourceOptions = sourceOptions;
                    this.retina = new Retina(this);
                    this.canvas = new Canvas(this);
                    this.particles = new Particles(this);
                    this.drawer = new FrameManager(this);
                    this.pathGenerator = {
                        generate: () => {
                            const v = Vector.create(0, 0);
                            v.length = Math.random();
                            v.angle = Math.random() * Math.PI * 2;
                            return v;
                        },
                        init: () => { },
                        update: () => { }
                    };
                    this.interactivity = {
                        mouse: {
                            clicking: false,
                            inside: false
                        }
                    };
                    this.bubble = {};
                    this.repulse = {
                        particles: []
                    };
                    this.attract = {
                        particles: []
                    };
                    this.plugins = new Map();
                    this.drawers = new Map();
                    this.density = 1;
                    this._options = new Options();
                    this.actualOptions = new Options();

                    for (const preset of presets) {
                        this._options.load(Plugins.getPreset(preset));
                    }

                    const shapes = Plugins.getSupportedShapes();

                    for (const type of shapes) {
                        const drawer = Plugins.getShapeDrawer(type);

                        if (drawer) {
                            this.drawers.set(type, drawer);
                        }
                    }

                    if (this._options) {
                        this._options.load(this._sourceOptions);
                    }

                    this.eventListeners = new EventListeners(this);

                    if (typeof IntersectionObserver !== "undefined" && IntersectionObserver) {
                        this.intersectionObserver = new IntersectionObserver(entries => this.intersectionManager(entries));
                    }
                }

                get options() {
                    return this._options;
                }

                get sourceOptions() {
                    return this._sourceOptions;
                }

                play(force) {
                    const needsUpdate = this.paused || force;

                    if (this.firstStart && !this.actualOptions.autoPlay) {
                        this.firstStart = false;
                        return;
                    }

                    if (this.paused) {
                        this.paused = false;
                    }

                    if (needsUpdate) {
                        for (const [, plugin] of this.plugins) {
                            if (plugin.play) {
                                plugin.play();
                            }
                        }
                    }

                    this.draw(needsUpdate || false);
                }

                pause() {
                    if (this.drawAnimationFrame !== undefined) {
                        cancelAnimation()(this.drawAnimationFrame);
                        delete this.drawAnimationFrame;
                    }

                    if (this.paused) {
                        return;
                    }

                    for (const [, plugin] of this.plugins) {
                        if (plugin.pause) {
                            plugin.pause();
                        }
                    }

                    if (!this.pageHidden) {
                        this.paused = true;
                    }
                }

                draw(force) {
                    let refreshTime = force;
                    this.drawAnimationFrame = animate()(timestamp => {
                        if (refreshTime) {
                            this.lastFrameTime = undefined;
                            refreshTime = false;
                        }

                        this.drawer.nextFrame(timestamp);
                    });
                }

                getAnimationStatus() {
                    return !this.paused && !this.pageHidden;
                }

                setNoise(noiseOrGenerator, init, update) {
                    this.setPath(noiseOrGenerator, init, update);
                }

                setPath(pathOrGenerator, init, update) {
                    if (!pathOrGenerator) {
                        return;
                    }

                    if (typeof pathOrGenerator === "function") {
                        this.pathGenerator.generate = pathOrGenerator;

                        if (init) {
                            this.pathGenerator.init = init;
                        }

                        if (update) {
                            this.pathGenerator.update = update;
                        }
                    } else {
                        if (pathOrGenerator.generate) {
                            this.pathGenerator.generate = pathOrGenerator.generate;
                        }

                        if (pathOrGenerator.init) {
                            this.pathGenerator.init = pathOrGenerator.init;
                        }

                        if (pathOrGenerator.update) {
                            this.pathGenerator.update = pathOrGenerator.update;
                        }
                    }
                }

                destroy() {
                    this.stop();
                    this.canvas.destroy();

                    for (const [, drawer] of this.drawers) {
                        if (drawer.destroy) {
                            drawer.destroy(this);
                        }
                    }

                    for (const key of this.drawers.keys()) {
                        this.drawers.delete(key);
                    }

                    this.destroyed = true;
                }

                exportImg(callback) {
                    this.exportImage(callback);
                }

                exportImage(callback, type, quality) {
                    var _a;

                    return (_a = this.canvas.element) === null || _a === void 0 ? void 0 : _a.toBlob(callback, type !== null && type !== void 0 ? type : "image/png", quality);
                }

                exportConfiguration() {
                    return JSON.stringify(this.actualOptions, undefined, 2);
                }

                refresh() {
                    this.stop();
                    return this.start();
                }

                reset() {
                    this._options = new Options();
                    return this.refresh();
                }

                stop() {
                    if (!this.started) {
                        return;
                    }

                    this.firstStart = true;
                    this.started = false;
                    this.eventListeners.removeListeners();
                    this.pause();
                    this.particles.clear();
                    this.canvas.clear();

                    if (this.interactivity.element instanceof HTMLElement && this.intersectionObserver) {
                        this.intersectionObserver.observe(this.interactivity.element);
                    }

                    for (const [, plugin] of this.plugins) {
                        if (plugin.stop) {
                            plugin.stop();
                        }
                    }

                    for (const key of this.plugins.keys()) {
                        this.plugins.delete(key);
                    }

                    this.particles.linksColors = new Map();
                    delete this.particles.grabLineColor;
                    delete this.particles.linksColor;
                }

                loadTheme(name) {
                    return Container_awaiter(this, void 0, void 0, function* () {
                        this.currentTheme = name;
                        yield this.refresh();
                    });
                }

                start() {
                    return Container_awaiter(this, void 0, void 0, function* () {
                        if (this.started) {
                            return;
                        }

                        yield this.init();
                        this.started = true;
                        this.eventListeners.addListeners();

                        if (this.interactivity.element instanceof HTMLElement && this.intersectionObserver) {
                            this.intersectionObserver.observe(this.interactivity.element);
                        }

                        for (const [, plugin] of this.plugins) {
                            if (plugin.startAsync !== undefined) {
                                yield plugin.startAsync();
                            } else if (plugin.start !== undefined) {
                                plugin.start();
                            }
                        }

                        this.play();
                    });
                }

                addClickHandler(callback) {
                    const el = this.interactivity.element;

                    if (!el) {
                        return;
                    }

                    const clickOrTouchHandler = (e, pos) => {
                        if (this.destroyed) {
                            return;
                        }

                        const pxRatio = this.retina.pixelRatio;
                        const posRetina = {
                            x: pos.x * pxRatio,
                            y: pos.y * pxRatio
                        };
                        const particles = this.particles.quadTree.queryCircle(posRetina, this.retina.sizeValue);
                        callback(e, particles);
                    };

                    const clickHandler = e => {
                        if (this.destroyed) {
                            return;
                        }

                        const mouseEvent = e;
                        const pos = {
                            x: mouseEvent.offsetX || mouseEvent.clientX,
                            y: mouseEvent.offsetY || mouseEvent.clientY
                        };
                        clickOrTouchHandler(e, pos);
                    };

                    const touchStartHandler = () => {
                        if (this.destroyed) {
                            return;
                        }

                        touched = true;
                        touchMoved = false;
                    };

                    const touchMoveHandler = () => {
                        if (this.destroyed) {
                            return;
                        }

                        touchMoved = true;
                    };

                    const touchEndHandler = e => {
                        var _a, _b, _c;

                        if (this.destroyed) {
                            return;
                        }

                        if (touched && !touchMoved) {
                            const touchEvent = e;
                            const lastTouch = touchEvent.touches[touchEvent.touches.length - 1];
                            const canvasRect = (_a = this.canvas.element) === null || _a === void 0 ? void 0 : _a.getBoundingClientRect();
                            const pos = {
                                x: lastTouch.clientX - ((_b = canvasRect === null || canvasRect === void 0 ? void 0 : canvasRect.left) !== null && _b !== void 0 ? _b : 0),
                                y: lastTouch.clientY - ((_c = canvasRect === null || canvasRect === void 0 ? void 0 : canvasRect.top) !== null && _c !== void 0 ? _c : 0)
                            };
                            clickOrTouchHandler(e, pos);
                        }

                        touched = false;
                        touchMoved = false;
                    };

                    const touchCancelHandler = () => {
                        if (this.destroyed) {
                            return;
                        }

                        touched = false;
                        touchMoved = false;
                    };

                    let touched = false;
                    let touchMoved = false;
                    el.addEventListener("click", clickHandler);
                    el.addEventListener("touchstart", touchStartHandler);
                    el.addEventListener("touchmove", touchMoveHandler);
                    el.addEventListener("touchend", touchEndHandler);
                    el.addEventListener("touchcancel", touchCancelHandler);
                }

                init() {
                    return Container_awaiter(this, void 0, void 0, function* () {
                        this.actualOptions = new Options();
                        this.actualOptions.load(this._options);
                        this.retina.init();
                        this.canvas.init();
                        this.actualOptions.setResponsive(this.canvas.size.width, this.retina.pixelRatio, this._options);
                        this.actualOptions.setTheme(this.currentTheme);
                        this.canvas.initBackground();
                        this.canvas.resize();
                        this.duration = getRangeValue(this.actualOptions.duration);
                        this.lifeTime = 0;
                        this.fpsLimit = this.actualOptions.fpsLimit > 0 ? this.actualOptions.fpsLimit : 60;
                        const availablePlugins = Plugins.getAvailablePlugins(this);

                        for (const [id, plugin] of availablePlugins) {
                            this.plugins.set(id, plugin);
                        }

                        for (const [, drawer] of this.drawers) {
                            if (drawer.init) {
                                yield drawer.init(this);
                            }
                        }

                        for (const [, plugin] of this.plugins) {
                            if (plugin.init) {
                                plugin.init(this.actualOptions);
                            } else if (plugin.initAsync !== undefined) {
                                yield plugin.initAsync(this.actualOptions);
                            }
                        }

                        this.particles.init();
                        this.particles.setDensity();

                        for (const [, plugin] of this.plugins) {
                            if (plugin.particlesSetup !== undefined) {
                                plugin.particlesSetup();
                            }
                        }
                    });
                }

                intersectionManager(entries) {
                    if (!this.actualOptions.pauseOnOutsideViewport) {
                        return;
                    }

                    for (const entry of entries) {
                        if (entry.target !== this.interactivity.element) {
                            continue;
                        }

                        if (entry.isIntersecting) {
                            this.play();
                        } else {
                            this.pause();
                        }
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Core/Loader.js
            var Loader_awaiter = undefined && undefined.__awaiter || function (thisArg, _arguments, P, generator) {
                function adopt(value) {
                    return value instanceof P ? value : new P(function (resolve) {
                        resolve(value);
                    });
                }

                return new (P || (P = Promise))(function (resolve, reject) {
                    function fulfilled(value) {
                        try {
                            step(generator.next(value));
                        } catch (e) {
                            reject(e);
                        }
                    }

                    function rejected(value) {
                        try {
                            step(generator["throw"](value));
                        } catch (e) {
                            reject(e);
                        }
                    }

                    function step(result) {
                        result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected);
                    }

                    step((generator = generator.apply(thisArg, _arguments || [])).next());
                });
            };



            const tsParticlesDom = [];

            function fetchError(statusCode) {
                console.error(`Error tsParticles - fetch status: ${statusCode}`);
                console.error("Error tsParticles - File config not found");
            }

            class Loader {
                static dom() {
                    return tsParticlesDom;
                }

                static domItem(index) {
                    const dom = Loader.dom();
                    const item = dom[index];

                    if (item && !item.destroyed) {
                        return item;
                    }

                    dom.splice(index, 1);
                }

                static load(tagId, options, index) {
                    return Loader_awaiter(this, void 0, void 0, function* () {
                        let domContainer = document.getElementById(tagId);

                        if (!domContainer) {
                            domContainer = document.createElement("div");
                            domContainer.id = tagId;
                            document.append(domContainer);
                        }

                        return Loader.set(tagId, domContainer, options, index);
                    });
                }

                static set(id, domContainer, options, index) {
                    return Loader_awaiter(this, void 0, void 0, function* () {
                        const currentOptions = options instanceof Array ? itemFromArray(options, index) : options;
                        const dom = Loader.dom();
                        const oldIndex = dom.findIndex(v => v.id === id);

                        if (oldIndex >= 0) {
                            const old = Loader.domItem(oldIndex);

                            if (old && !old.destroyed) {
                                old.destroy();
                                dom.splice(oldIndex, 1);
                            }
                        }

                        let canvasEl;
                        let generatedCanvas;

                        if (domContainer.tagName.toLowerCase() === "canvas") {
                            canvasEl = domContainer;
                            generatedCanvas = false;
                        } else {
                            const existingCanvases = domContainer.getElementsByTagName("canvas");

                            if (existingCanvases.length) {
                                canvasEl = existingCanvases[0];

                                if (!canvasEl.className) {
                                    canvasEl.className = Constants.canvasClass;
                                }

                                generatedCanvas = false;
                            } else {
                                generatedCanvas = true;
                                canvasEl = document.createElement("canvas");
                                canvasEl.className = Constants.canvasClass;
                                canvasEl.style.width = "100%";
                                canvasEl.style.height = "100%";
                                domContainer.appendChild(canvasEl);
                            }
                        }

                        const newItem = new Container(id, currentOptions);

                        if (oldIndex >= 0) {
                            dom.splice(oldIndex, 0, newItem);
                        } else {
                            dom.push(newItem);
                        }

                        newItem.canvas.loadCanvas(canvasEl, generatedCanvas);
                        yield newItem.start();
                        return newItem;
                    });
                }

                static loadJSON(tagId, jsonUrl, index) {
                    return Loader_awaiter(this, void 0, void 0, function* () {
                        const url = jsonUrl instanceof Array ? itemFromArray(jsonUrl, index) : jsonUrl;
                        const response = yield fetch(url);

                        if (response.ok) {
                            return Loader.load(tagId, yield response.json());
                        } else {
                            fetchError(response.status);
                        }
                    });
                }

                static setJSON(id, domContainer, jsonUrl, index) {
                    return Loader_awaiter(this, void 0, void 0, function* () {
                        const url = jsonUrl instanceof Array ? itemFromArray(jsonUrl, index) : jsonUrl;
                        const response = yield fetch(url);

                        if (response.ok) {
                            const options = yield response.json();
                            return Loader.set(id, domContainer, options);
                        } else {
                            fetchError(response.status);
                        }
                    });
                }

                static setOnClickHandler(callback) {
                    const dom = Loader.dom();

                    if (dom.length === 0) {
                        throw new Error("Can only set click handlers after calling tsParticles.load() or tsParticles.loadJSON()");
                    }

                    for (const domItem of dom) {
                        domItem.addClickHandler(callback);
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/main.js
            var main_awaiter = undefined && undefined.__awaiter || function (thisArg, _arguments, P, generator) {
                function adopt(value) {
                    return value instanceof P ? value : new P(function (resolve) {
                        resolve(value);
                    });
                }

                return new (P || (P = Promise))(function (resolve, reject) {
                    function fulfilled(value) {
                        try {
                            step(generator.next(value));
                        } catch (e) {
                            reject(e);
                        }
                    }

                    function rejected(value) {
                        try {
                            step(generator["throw"](value));
                        } catch (e) {
                            reject(e);
                        }
                    }

                    function step(result) {
                        result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected);
                    }

                    step((generator = generator.apply(thisArg, _arguments || [])).next());
                });
            };

            var __classPrivateFieldSet = undefined && undefined.__classPrivateFieldSet || function (receiver, state, value, kind, f) {
                if (kind === "m") throw new TypeError("Private method is not writable");
                if (kind === "a" && !f) throw new TypeError("Private accessor was defined without a setter");
                if (typeof state === "function" ? receiver !== state || !f : !state.has(receiver)) throw new TypeError("Cannot write private member to an object whose class did not declare it");
                return kind === "a" ? f.call(receiver, value) : f ? f.value = value : state.set(receiver, value), value;
            };

            var __classPrivateFieldGet = undefined && undefined.__classPrivateFieldGet || function (receiver, state, kind, f) {
                if (kind === "a" && !f) throw new TypeError("Private accessor was defined without a getter");
                if (typeof state === "function" ? receiver !== state || !f : !state.has(receiver)) throw new TypeError("Cannot read private member from an object whose class did not declare it");
                return kind === "m" ? f : kind === "a" ? f.call(receiver) : f ? f.value : state.get(receiver);
            };

            var _Main_initialized;



            class Main {
                constructor() {
                    _Main_initialized.set(this, void 0);

                    __classPrivateFieldSet(this, _Main_initialized, false, "f");
                }

                init() {
                    if (!__classPrivateFieldGet(this, _Main_initialized, "f")) {
                        __classPrivateFieldSet(this, _Main_initialized, true, "f");
                    }
                }

                loadFromArray(tagId, options, index) {
                    return main_awaiter(this, void 0, void 0, function* () {
                        return Loader.load(tagId, options, index);
                    });
                }

                load(tagId, options) {
                    return main_awaiter(this, void 0, void 0, function* () {
                        return Loader.load(tagId, options);
                    });
                }

                set(id, element, options) {
                    return main_awaiter(this, void 0, void 0, function* () {
                        return Loader.set(id, element, options);
                    });
                }

                loadJSON(tagId, pathConfigJson, index) {
                    return Loader.loadJSON(tagId, pathConfigJson, index);
                }

                setJSON(id, element, pathConfigJson, index) {
                    return main_awaiter(this, void 0, void 0, function* () {
                        return Loader.setJSON(id, element, pathConfigJson, index);
                    });
                }

                setOnClickHandler(callback) {
                    Loader.setOnClickHandler(callback);
                }

                dom() {
                    return Loader.dom();
                }

                domItem(index) {
                    return Loader.domItem(index);
                }

                addShape(shape, drawer, init, afterEffect, destroy) {
                    let customDrawer;

                    if (typeof drawer === "function") {
                        customDrawer = {
                            afterEffect: afterEffect,
                            destroy: destroy,
                            draw: drawer,
                            init: init
                        };
                    } else {
                        customDrawer = drawer;
                    }

                    Plugins.addShapeDrawer(shape, customDrawer);
                }

                addPreset(preset, options, override = false) {
                    Plugins.addPreset(preset, options, override);
                }

                addPlugin(plugin) {
                    Plugins.addPlugin(plugin);
                }

                addPathGenerator(name, generator) {
                    Plugins.addPathGenerator(name, generator);
                }

                addInteractor(name, interactorInitializer) {
                    Plugins.addInteractor(name, interactorInitializer);
                }

                addParticleUpdater(name, updaterInitializer) {
                    Plugins.addParticleUpdater(name, updaterInitializer);
                }

            }
            _Main_initialized = new WeakMap();
            ;// CONCATENATED MODULE: ./dist/browser/Plugins/Absorbers/AbsorberInstance.js



            class AbsorberInstance {
                constructor(absorbers, container, options, position) {
                    var _a, _b, _c;

                    this.absorbers = absorbers;
                    this.container = container;
                    this.initialPosition = position ? Vector.create(position.x, position.y) : undefined;
                    this.options = options;
                    this.dragging = false;
                    this.name = this.options.name;
                    this.opacity = this.options.opacity;
                    this.size = getRangeValue(options.size.value) * container.retina.pixelRatio;
                    this.mass = this.size * options.size.density * container.retina.reduceFactor;
                    const limit = options.size.limit;
                    this.limit = limit !== undefined ? limit * container.retina.pixelRatio * container.retina.reduceFactor : limit;
                    const color = typeof options.color === "string" ? {
                        value: options.color
                    } : options.color;
                    this.color = (_a = colorToRgb(color)) !== null && _a !== void 0 ? _a : {
                        b: 0,
                        g: 0,
                        r: 0
                    };
                    this.position = (_c = (_b = this.initialPosition) === null || _b === void 0 ? void 0 : _b.copy()) !== null && _c !== void 0 ? _c : this.calcPosition();
                }

                attract(particle) {
                    const container = this.container;
                    const options = this.options;

                    if (options.draggable) {
                        const mouse = container.interactivity.mouse;

                        if (mouse.clicking && mouse.downPosition) {
                            const mouseDist = getDistance(this.position, mouse.downPosition);

                            if (mouseDist <= this.size) {
                                this.dragging = true;
                            }
                        } else {
                            this.dragging = false;
                        }

                        if (this.dragging && mouse.position) {
                            this.position.x = mouse.position.x;
                            this.position.y = mouse.position.y;
                        }
                    }

                    const pos = particle.getPosition();
                    const {
                        dx,
                        dy,
                        distance
                    } = getDistances(this.position, pos);
                    const v = Vector.create(dx, dy);
                    v.length = this.mass / Math.pow(distance, 2) * container.retina.reduceFactor;

                    if (distance < this.size + particle.getRadius()) {
                        const sizeFactor = particle.getRadius() * 0.033 * container.retina.pixelRatio;

                        if (this.size > particle.getRadius() && distance < this.size - particle.getRadius() || particle.absorberOrbit !== undefined && particle.absorberOrbit.length < 0) {
                            if (options.destroy) {
                                particle.destroy();
                            } else {
                                particle.needsNewPosition = true;
                                this.updateParticlePosition(particle, v);
                            }
                        } else {
                            if (options.destroy) {
                                particle.size.value -= sizeFactor;
                            }

                            this.updateParticlePosition(particle, v);
                        }

                        if (this.limit === undefined || this.size < this.limit) {
                            this.size += sizeFactor;
                        }

                        this.mass += sizeFactor * this.options.size.density * container.retina.reduceFactor;
                    } else {
                        this.updateParticlePosition(particle, v);
                    }
                }

                resize() {
                    const initialPosition = this.initialPosition;
                    this.position = initialPosition && isPointInside(initialPosition, this.container.canvas.size) ? initialPosition : this.calcPosition();
                }

                draw(context) {
                    context.translate(this.position.x, this.position.y);
                    context.beginPath();
                    context.arc(0, 0, this.size, 0, Math.PI * 2, false);
                    context.closePath();
                    context.fillStyle = getStyleFromRgb(this.color, this.opacity);
                    context.fill();
                }

                calcPosition() {
                    var _a, _b;

                    const container = this.container;
                    const percentPosition = this.options.position;
                    return Vector.create(((_a = percentPosition === null || percentPosition === void 0 ? void 0 : percentPosition.x) !== null && _a !== void 0 ? _a : Math.random() * 100) / 100 * container.canvas.size.width, ((_b = percentPosition === null || percentPosition === void 0 ? void 0 : percentPosition.y) !== null && _b !== void 0 ? _b : Math.random() * 100) / 100 * container.canvas.size.height);
                }

                updateParticlePosition(particle, v) {
                    var _a;

                    if (particle.destroyed) {
                        return;
                    }

                    const container = this.container;
                    const canvasSize = container.canvas.size;

                    if (particle.needsNewPosition) {
                        const pSize = particle.getRadius();
                        particle.position.x = (canvasSize.width - pSize * 2) * (1 + (Math.random() * 0.2 - 0.1)) + pSize;
                        particle.position.y = (canvasSize.height - pSize * 2) * (1 + (Math.random() * 0.2 - 0.1)) + pSize;
                        particle.needsNewPosition = false;
                    }

                    if (this.options.orbits) {
                        if (particle.absorberOrbit === undefined) {
                            particle.absorberOrbit = Vector.create(0, 0);
                            particle.absorberOrbit.length = getDistance(particle.getPosition(), this.position);
                            particle.absorberOrbit.angle = Math.random() * Math.PI * 2;
                        }

                        if (particle.absorberOrbit.length <= this.size && !this.options.destroy) {
                            const minSize = Math.min(canvasSize.width, canvasSize.height);
                            particle.absorberOrbit.length = minSize * (1 + (Math.random() * 0.2 - 0.1));
                        }

                        if (particle.absorberOrbitDirection === undefined) {
                            particle.absorberOrbitDirection = particle.velocity.x >= 0 ? RotateDirection.clockwise : RotateDirection.counterClockwise;
                        }

                        const orbitRadius = particle.absorberOrbit.length;
                        const orbitAngle = particle.absorberOrbit.angle;
                        const orbitDirection = particle.absorberOrbitDirection;
                        particle.velocity.x = 0;
                        particle.velocity.y = 0;
                        const updateFunc = {
                            x: orbitDirection === RotateDirection.clockwise ? Math.cos : Math.sin,
                            y: orbitDirection === RotateDirection.clockwise ? Math.sin : Math.cos
                        };
                        particle.position.x = this.position.x + orbitRadius * updateFunc.x(orbitAngle);
                        particle.position.y = this.position.y + orbitRadius * updateFunc.y(orbitAngle);
                        particle.absorberOrbit.length -= v.length;
                        particle.absorberOrbit.angle += ((_a = particle.moveSpeed) !== null && _a !== void 0 ? _a : 0) * container.retina.pixelRatio / 100 * container.retina.reduceFactor;
                    } else {
                        const addV = Vector.origin;
                        addV.length = v.length;
                        addV.angle = v.angle;
                        particle.velocity.addTo(addV);
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Plugins/Absorbers/Options/Classes/AbsorberSize.js

            class AbsorberSize extends ValueWithRandom {
                constructor() {
                    super();
                    this.density = 5;
                    this.random.minimumValue = 1;
                    this.value = 50;
                }

                load(data) {
                    if (!data) {
                        return;
                    }

                    super.load(data);

                    if (data.density !== undefined) {
                        this.density = data.density;
                    }

                    if (data.limit !== undefined) {
                        this.limit = data.limit;
                    }

                    if (data.limit !== undefined) {
                        this.limit = data.limit;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Plugins/Absorbers/Options/Classes/Absorber.js


            class Absorber {
                constructor() {
                    this.color = new OptionsColor();
                    this.color.value = "#000000";
                    this.draggable = false;
                    this.opacity = 1;
                    this.destroy = true;
                    this.orbits = false;
                    this.size = new AbsorberSize();
                }

                load(data) {
                    if (data === undefined) {
                        return;
                    }

                    if (data.color !== undefined) {
                        this.color = OptionsColor.create(this.color, data.color);
                    }

                    if (data.draggable !== undefined) {
                        this.draggable = data.draggable;
                    }

                    this.name = data.name;

                    if (data.opacity !== undefined) {
                        this.opacity = data.opacity;
                    }

                    if (data.position !== undefined) {
                        this.position = {
                            x: data.position.x,
                            y: data.position.y
                        };
                    }

                    if (data.size !== undefined) {
                        this.size.load(data.size);
                    }

                    if (data.destroy !== undefined) {
                        this.destroy = data.destroy;
                    }

                    if (data.orbits !== undefined) {
                        this.orbits = data.orbits;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Plugins/Absorbers/Enums/AbsorberClickMode.js
            var AbsorberClickMode;

            (function (AbsorberClickMode) {
                AbsorberClickMode["absorber"] = "absorber";
            })(AbsorberClickMode || (AbsorberClickMode = {}));
            ;// CONCATENATED MODULE: ./dist/browser/Plugins/Absorbers/Enums/index.js

            ;// CONCATENATED MODULE: ./dist/browser/Plugins/Absorbers/Absorbers.js




            class Absorbers {
                constructor(container) {
                    this.container = container;
                    this.array = [];
                    this.absorbers = [];
                    this.interactivityAbsorbers = [];
                    const overridableContainer = container;

                    overridableContainer.getAbsorber = idxOrName => idxOrName === undefined || typeof idxOrName === "number" ? this.array[idxOrName || 0] : this.array.find(t => t.name === idxOrName);

                    overridableContainer.addAbsorber = (options, position) => this.addAbsorber(options, position);
                }

                init(options) {
                    var _a, _b;

                    if (!options) {
                        return;
                    }

                    if (options.absorbers) {
                        if (options.absorbers instanceof Array) {
                            this.absorbers = options.absorbers.map(s => {
                                const tmp = new Absorber();
                                tmp.load(s);
                                return tmp;
                            });
                        } else {
                            if (this.absorbers instanceof Array) {
                                this.absorbers = new Absorber();
                            }

                            this.absorbers.load(options.absorbers);
                        }
                    }

                    const interactivityAbsorbers = (_b = (_a = options.interactivity) === null || _a === void 0 ? void 0 : _a.modes) === null || _b === void 0 ? void 0 : _b.absorbers;

                    if (interactivityAbsorbers) {
                        if (interactivityAbsorbers instanceof Array) {
                            this.interactivityAbsorbers = interactivityAbsorbers.map(s => {
                                const tmp = new Absorber();
                                tmp.load(s);
                                return tmp;
                            });
                        } else {
                            if (this.interactivityAbsorbers instanceof Array) {
                                this.interactivityAbsorbers = new Absorber();
                            }

                            this.interactivityAbsorbers.load(interactivityAbsorbers);
                        }
                    }

                    if (this.absorbers instanceof Array) {
                        for (const absorberOptions of this.absorbers) {
                            this.addAbsorber(absorberOptions);
                        }
                    } else {
                        this.addAbsorber(this.absorbers);
                    }
                }

                particleUpdate(particle) {
                    for (const absorber of this.array) {
                        absorber.attract(particle);

                        if (particle.destroyed) {
                            break;
                        }
                    }
                }

                draw(context) {
                    for (const absorber of this.array) {
                        context.save();
                        absorber.draw(context);
                        context.restore();
                    }
                }

                stop() {
                    this.array = [];
                }

                resize() {
                    for (const absorber of this.array) {
                        absorber.resize();
                    }
                }

                handleClickMode(mode) {
                    const container = this.container;
                    const absorberOptions = this.absorbers;
                    const modeAbsorbers = this.interactivityAbsorbers;

                    if (mode === AbsorberClickMode.absorber) {
                        let absorbersModeOptions;

                        if (modeAbsorbers instanceof Array) {
                            if (modeAbsorbers.length > 0) {
                                absorbersModeOptions = itemFromArray(modeAbsorbers);
                            }
                        } else {
                            absorbersModeOptions = modeAbsorbers;
                        }

                        const absorbersOptions = absorbersModeOptions !== null && absorbersModeOptions !== void 0 ? absorbersModeOptions : absorberOptions instanceof Array ? itemFromArray(absorberOptions) : absorberOptions;
                        const aPosition = container.interactivity.mouse.clickPosition;
                        this.addAbsorber(absorbersOptions, aPosition);
                    }
                }

                addAbsorber(options, position) {
                    const absorber = new AbsorberInstance(this, this.container, options, position);
                    this.array.push(absorber);
                    return absorber;
                }

                removeAbsorber(absorber) {
                    const index = this.array.indexOf(absorber);

                    if (index >= 0) {
                        this.array.splice(index, 1);
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Plugins/Absorbers/plugin.js





            class Plugin {
                constructor() {
                    this.id = "absorbers";
                }

                getPlugin(container) {
                    return new Absorbers(container);
                }

                needsPlugin(options) {
                    var _a, _b, _c;

                    if (options === undefined) {
                        return false;
                    }

                    const absorbers = options.absorbers;
                    let loadAbsorbers = false;

                    if (absorbers instanceof Array) {
                        if (absorbers.length) {
                            loadAbsorbers = true;
                        }
                    } else if (absorbers !== undefined) {
                        loadAbsorbers = true;
                    } else if (((_c = (_b = (_a = options.interactivity) === null || _a === void 0 ? void 0 : _a.events) === null || _b === void 0 ? void 0 : _b.onClick) === null || _c === void 0 ? void 0 : _c.mode) && isInArray(AbsorberClickMode.absorber, options.interactivity.events.onClick.mode)) {
                        loadAbsorbers = true;
                    }

                    return loadAbsorbers;
                }

                loadOptions(options, source) {
                    var _a, _b;

                    if (!this.needsPlugin(options) && !this.needsPlugin(source)) {
                        return;
                    }

                    const optionsCast = options;

                    if (source === null || source === void 0 ? void 0 : source.absorbers) {
                        if ((source === null || source === void 0 ? void 0 : source.absorbers) instanceof Array) {
                            optionsCast.absorbers = source === null || source === void 0 ? void 0 : source.absorbers.map(s => {
                                const tmp = new Absorber();
                                tmp.load(s);
                                return tmp;
                            });
                        } else {
                            let absorberOptions = optionsCast.absorbers;

                            if ((absorberOptions === null || absorberOptions === void 0 ? void 0 : absorberOptions.load) === undefined) {
                                optionsCast.absorbers = absorberOptions = new Absorber();
                            }

                            absorberOptions.load(source === null || source === void 0 ? void 0 : source.absorbers);
                        }
                    }

                    const interactivityAbsorbers = (_b = (_a = source === null || source === void 0 ? void 0 : source.interactivity) === null || _a === void 0 ? void 0 : _a.modes) === null || _b === void 0 ? void 0 : _b.absorbers;

                    if (interactivityAbsorbers) {
                        if (interactivityAbsorbers instanceof Array) {
                            optionsCast.interactivity.modes.absorbers = interactivityAbsorbers.map(s => {
                                const tmp = new Absorber();
                                tmp.load(s);
                                return tmp;
                            });
                        } else {
                            let absorberOptions = optionsCast.interactivity.modes.absorbers;

                            if ((absorberOptions === null || absorberOptions === void 0 ? void 0 : absorberOptions.load) === undefined) {
                                optionsCast.interactivity.modes.absorbers = absorberOptions = new Absorber();
                            }

                            absorberOptions.load(interactivityAbsorbers);
                        }
                    }
                }

            }

            function loadAbsorbersPlugin(tsParticles) {
                const plugin = new Plugin();
                tsParticles.addPlugin(plugin);
            }
            ;// CONCATENATED MODULE: ./dist/browser/Plugins/Emitters/Options/Classes/EmitterSize.js

            class EmitterSize {
                constructor() {
                    this.mode = SizeMode.percent;
                    this.height = 0;
                    this.width = 0;
                }

                load(data) {
                    if (data === undefined) {
                        return;
                    }

                    if (data.mode !== undefined) {
                        this.mode = data.mode;
                    }

                    if (data.height !== undefined) {
                        this.height = data.height;
                    }

                    if (data.width !== undefined) {
                        this.width = data.width;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Plugins/Emitters/EmitterInstance.js




            function randomCoordinate(position, offset) {
                return position + offset * (Math.random() - 0.5);
            }

            function randomPosition(position, offset) {
                return {
                    x: randomCoordinate(position.x, offset.x),
                    y: randomCoordinate(position.y, offset.y)
                };
            }

            class EmitterInstance {
                constructor(emitters, container, emitterOptions, position) {
                    var _a, _b, _c, _d, _e, _f;

                    var _g;

                    this.emitters = emitters;
                    this.container = container;
                    this.firstSpawn = true;
                    this.currentDuration = 0;
                    this.currentEmitDelay = 0;
                    this.currentSpawnDelay = 0;
                    this.initialPosition = position;
                    this.emitterOptions = deepExtend({}, emitterOptions);
                    this.spawnDelay = ((_a = this.emitterOptions.life.delay) !== null && _a !== void 0 ? _a : 0) * 1000 / this.container.retina.reduceFactor;
                    this.position = (_b = this.initialPosition) !== null && _b !== void 0 ? _b : this.calcPosition();
                    this.name = emitterOptions.name;
                    let particlesOptions = deepExtend({}, this.emitterOptions.particles);
                    particlesOptions !== null && particlesOptions !== void 0 ? particlesOptions : particlesOptions = {};
                    (_c = particlesOptions.move) !== null && _c !== void 0 ? _c : particlesOptions.move = {};
                    (_d = (_g = particlesOptions.move).direction) !== null && _d !== void 0 ? _d : _g.direction = this.emitterOptions.direction;

                    if (this.emitterOptions.spawnColor !== undefined) {
                        this.spawnColor = colorToHsl(this.emitterOptions.spawnColor);
                    }

                    this.paused = !this.emitterOptions.autoPlay;
                    this.particlesOptions = particlesOptions;
                    this.size = (_e = this.emitterOptions.size) !== null && _e !== void 0 ? _e : (() => {
                        const size = new EmitterSize();
                        size.load({
                            height: 0,
                            mode: SizeMode.percent,
                            width: 0
                        });
                        return size;
                    })();
                    this.lifeCount = (_f = this.emitterOptions.life.count) !== null && _f !== void 0 ? _f : -1;
                    this.immortal = this.lifeCount <= 0;
                    this.play();
                }

                externalPlay() {
                    this.paused = false;
                    this.play();
                }

                externalPause() {
                    this.paused = true;
                    this.pause();
                }

                play() {
                    if (this.paused) {
                        return;
                    }

                    if (this.container.retina.reduceFactor && (this.lifeCount > 0 || this.immortal || !this.emitterOptions.life.count)) {
                        if (this.emitDelay === undefined) {
                            const delay = getRangeValue(this.emitterOptions.rate.delay);
                            this.emitDelay = 1000 * delay / this.container.retina.reduceFactor;
                        }

                        if (this.lifeCount > 0 || this.immortal) {
                            this.prepareToDie();
                        }
                    }
                }

                pause() {
                    if (this.paused) {
                        return;
                    }

                    delete this.emitDelay;
                }

                resize() {
                    const initialPosition = this.initialPosition;
                    this.position = initialPosition && isPointInside(initialPosition, this.container.canvas.size) ? initialPosition : this.calcPosition();
                }

                update(delta) {
                    var _a, _b, _c;

                    if (this.paused) {
                        return;
                    }

                    if (this.firstSpawn) {
                        this.firstSpawn = false;
                        this.currentSpawnDelay = (_a = this.spawnDelay) !== null && _a !== void 0 ? _a : 0;
                        this.currentEmitDelay = (_b = this.emitDelay) !== null && _b !== void 0 ? _b : 0;
                        delta.value = 0;
                    }

                    if (this.duration !== undefined) {
                        this.currentDuration += delta.value;

                        if (this.currentDuration >= this.duration) {
                            this.pause();

                            if (this.spawnDelay !== undefined) {
                                delete this.spawnDelay;
                            }

                            if (!this.immortal) {
                                this.lifeCount--;
                            }

                            if (this.lifeCount > 0 || this.immortal) {
                                this.position = this.calcPosition();
                                this.spawnDelay = ((_c = this.emitterOptions.life.delay) !== null && _c !== void 0 ? _c : 0) * 1000 / this.container.retina.reduceFactor;
                            } else {
                                this.destroy();
                            }

                            this.currentDuration -= this.duration;
                            delete this.duration;
                        }
                    }

                    if (this.spawnDelay !== undefined) {
                        this.currentSpawnDelay += delta.value;

                        if (this.currentSpawnDelay >= this.spawnDelay) {
                            this.play();
                            this.currentSpawnDelay -= this.currentSpawnDelay;
                            delete this.spawnDelay;
                        }
                    }

                    if (this.emitDelay !== undefined) {
                        this.currentEmitDelay += delta.value;

                        if (this.currentEmitDelay >= this.emitDelay) {
                            this.emit();
                            this.currentEmitDelay -= this.emitDelay;
                        }
                    }
                }

                prepareToDie() {
                    var _a;

                    if (this.paused) {
                        return;
                    }

                    const duration = (_a = this.emitterOptions.life) === null || _a === void 0 ? void 0 : _a.duration;

                    if (this.container.retina.reduceFactor && (this.lifeCount > 0 || this.immortal) && duration !== undefined && duration > 0) {
                        this.duration = duration * 1000;
                    }
                }

                destroy() {
                    this.emitters.removeEmitter(this);
                }

                calcPosition() {
                    var _a, _b;

                    const container = this.container;
                    const percentPosition = this.emitterOptions.position;
                    return {
                        x: ((_a = percentPosition === null || percentPosition === void 0 ? void 0 : percentPosition.x) !== null && _a !== void 0 ? _a : Math.random() * 100) / 100 * container.canvas.size.width,
                        y: ((_b = percentPosition === null || percentPosition === void 0 ? void 0 : percentPosition.y) !== null && _b !== void 0 ? _b : Math.random() * 100) / 100 * container.canvas.size.height
                    };
                }

                emit() {
                    var _a;

                    if (this.paused) {
                        return;
                    }

                    const container = this.container;
                    const position = this.position;
                    const offset = {
                        x: this.size.mode === SizeMode.percent ? container.canvas.size.width * this.size.width / 100 : this.size.width,
                        y: this.size.mode === SizeMode.percent ? container.canvas.size.height * this.size.height / 100 : this.size.height
                    };
                    const quantity = getRangeValue(this.emitterOptions.rate.quantity);

                    for (let i = 0; i < quantity; i++) {
                        const particlesOptions = deepExtend({}, this.particlesOptions);

                        if (this.spawnColor !== undefined) {
                            const colorAnimation = (_a = this.emitterOptions.spawnColor) === null || _a === void 0 ? void 0 : _a.animation;

                            if (colorAnimation) {
                                const hueAnimation = colorAnimation;

                                if (hueAnimation.enable) {
                                    this.spawnColor.h = this.setColorAnimation(hueAnimation, this.spawnColor.h, 360);
                                } else {
                                    const hslAnimation = colorAnimation;
                                    this.spawnColor.h = this.setColorAnimation(hslAnimation.h, this.spawnColor.h, 360);
                                    this.spawnColor.s = this.setColorAnimation(hslAnimation.s, this.spawnColor.s, 100);
                                    this.spawnColor.l = this.setColorAnimation(hslAnimation.l, this.spawnColor.l, 100);
                                }
                            }

                            if (!particlesOptions.color) {
                                particlesOptions.color = {
                                    value: this.spawnColor
                                };
                            } else {
                                particlesOptions.color.value = this.spawnColor;
                            }
                        }

                        container.particles.addParticle(randomPosition(position, offset), particlesOptions);
                    }
                }

                setColorAnimation(animation, initValue, maxValue) {
                    var _a;

                    const container = this.container;

                    if (!animation.enable) {
                        return initValue;
                    }

                    const colorOffset = randomInRange(animation.offset);
                    const delay = getRangeValue(this.emitterOptions.rate.delay);
                    const emitFactor = 1000 * delay / container.retina.reduceFactor;
                    const colorSpeed = (_a = animation.speed) !== null && _a !== void 0 ? _a : 0;
                    return (initValue + colorSpeed * container.fpsLimit / emitFactor + colorOffset * 3.6) % maxValue;
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Plugins/Emitters/Options/Classes/EmitterRate.js

            class EmitterRate {
                constructor() {
                    this.quantity = 1;
                    this.delay = 0.1;
                }

                load(data) {
                    if (data === undefined) {
                        return;
                    }

                    if (data.quantity !== undefined) {
                        this.quantity = setRangeValue(data.quantity);
                    }

                    if (data.delay !== undefined) {
                        this.delay = setRangeValue(data.delay);
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Plugins/Emitters/Options/Classes/EmitterLife.js
            class EmitterLife {
                load(data) {
                    if (data === undefined) {
                        return;
                    }

                    if (data.count !== undefined) {
                        this.count = data.count;
                    }

                    if (data.delay !== undefined) {
                        this.delay = data.delay;
                    }

                    if (data.duration !== undefined) {
                        this.duration = data.duration;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Plugins/Emitters/Options/Classes/Emitter.js





            class Emitter {
                constructor() {
                    this.autoPlay = true;
                    this.life = new EmitterLife();
                    this.rate = new EmitterRate();
                }

                load(data) {
                    if (data === undefined) {
                        return;
                    }

                    if (data.autoPlay !== undefined) {
                        this.autoPlay = data.autoPlay;
                    }

                    if (data.size !== undefined) {
                        if (this.size === undefined) {
                            this.size = new EmitterSize();
                        }

                        this.size.load(data.size);
                    }

                    if (data.direction !== undefined) {
                        this.direction = data.direction;
                    }

                    this.life.load(data.life);
                    this.name = data.name;

                    if (data.particles !== undefined) {
                        this.particles = deepExtend({}, data.particles);
                    }

                    this.rate.load(data.rate);

                    if (data.position !== undefined) {
                        this.position = {
                            x: data.position.x,
                            y: data.position.y
                        };
                    }

                    if (data.spawnColor !== undefined) {
                        if (this.spawnColor === undefined) {
                            this.spawnColor = new AnimatableColor();
                        }

                        this.spawnColor.load(data.spawnColor);
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Plugins/Emitters/Enums/EmitterClickMode.js
            var EmitterClickMode;

            (function (EmitterClickMode) {
                EmitterClickMode["emitter"] = "emitter";
            })(EmitterClickMode || (EmitterClickMode = {}));
            ;// CONCATENATED MODULE: ./dist/browser/Plugins/Emitters/Enums/index.js

            ;// CONCATENATED MODULE: ./dist/browser/Plugins/Emitters/Emitters.js




            class Emitters {
                constructor(container) {
                    this.container = container;
                    this.array = [];
                    this.emitters = [];
                    this.interactivityEmitters = [];
                    const overridableContainer = container;

                    overridableContainer.getEmitter = idxOrName => idxOrName === undefined || typeof idxOrName === "number" ? this.array[idxOrName || 0] : this.array.find(t => t.name === idxOrName);

                    overridableContainer.addEmitter = (options, position) => this.addEmitter(options, position);

                    overridableContainer.playEmitter = idxOrName => {
                        const emitter = overridableContainer.getEmitter(idxOrName);

                        if (emitter) {
                            emitter.externalPlay();
                        }
                    };

                    overridableContainer.pauseEmitter = idxOrName => {
                        const emitter = overridableContainer.getEmitter(idxOrName);

                        if (emitter) {
                            emitter.externalPause();
                        }
                    };
                }

                init(options) {
                    var _a, _b;

                    if (!options) {
                        return;
                    }

                    if (options.emitters) {
                        if (options.emitters instanceof Array) {
                            this.emitters = options.emitters.map(s => {
                                const tmp = new Emitter();
                                tmp.load(s);
                                return tmp;
                            });
                        } else {
                            if (this.emitters instanceof Array) {
                                this.emitters = new Emitter();
                            }

                            this.emitters.load(options.emitters);
                        }
                    }

                    const interactivityEmitters = (_b = (_a = options.interactivity) === null || _a === void 0 ? void 0 : _a.modes) === null || _b === void 0 ? void 0 : _b.emitters;

                    if (interactivityEmitters) {
                        if (interactivityEmitters instanceof Array) {
                            this.interactivityEmitters = interactivityEmitters.map(s => {
                                const tmp = new Emitter();
                                tmp.load(s);
                                return tmp;
                            });
                        } else {
                            if (this.interactivityEmitters instanceof Array) {
                                this.interactivityEmitters = new Emitter();
                            }

                            this.interactivityEmitters.load(interactivityEmitters);
                        }
                    }

                    if (this.emitters instanceof Array) {
                        for (const emitterOptions of this.emitters) {
                            this.addEmitter(emitterOptions);
                        }
                    } else {
                        this.addEmitter(this.emitters);
                    }
                }

                play() {
                    for (const emitter of this.array) {
                        emitter.play();
                    }
                }

                pause() {
                    for (const emitter of this.array) {
                        emitter.pause();
                    }
                }

                stop() {
                    this.array = [];
                }

                update(delta) {
                    for (const emitter of this.array) {
                        emitter.update(delta);
                    }
                }

                handleClickMode(mode) {
                    const container = this.container;
                    const emitterOptions = this.emitters;
                    const modeEmitters = this.interactivityEmitters;

                    if (mode === EmitterClickMode.emitter) {
                        let emitterModeOptions;

                        if (modeEmitters instanceof Array) {
                            if (modeEmitters.length > 0) {
                                emitterModeOptions = itemFromArray(modeEmitters);
                            }
                        } else {
                            emitterModeOptions = modeEmitters;
                        }

                        const emittersOptions = emitterModeOptions !== null && emitterModeOptions !== void 0 ? emitterModeOptions : emitterOptions instanceof Array ? itemFromArray(emitterOptions) : emitterOptions;
                        const ePosition = container.interactivity.mouse.clickPosition;
                        this.addEmitter(deepExtend({}, emittersOptions), ePosition);
                    }
                }

                resize() {
                    for (const emitter of this.array) {
                        emitter.resize();
                    }
                }

                addEmitter(options, position) {
                    const emitter = new EmitterInstance(this, this.container, options, position);
                    this.array.push(emitter);
                    return emitter;
                }

                removeEmitter(emitter) {
                    const index = this.array.indexOf(emitter);

                    if (index >= 0) {
                        this.array.splice(index, 1);
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Plugins/Emitters/plugin.js





            class EmittersPlugin {
                constructor() {
                    this.id = "emitters";
                }

                getPlugin(container) {
                    return new Emitters(container);
                }

                needsPlugin(options) {
                    var _a, _b, _c;

                    if (options === undefined) {
                        return false;
                    }

                    const emitters = options.emitters;
                    return emitters instanceof Array && !!emitters.length || emitters !== undefined || !!((_c = (_b = (_a = options.interactivity) === null || _a === void 0 ? void 0 : _a.events) === null || _b === void 0 ? void 0 : _b.onClick) === null || _c === void 0 ? void 0 : _c.mode) && isInArray(EmitterClickMode.emitter, options.interactivity.events.onClick.mode);
                }

                loadOptions(options, source) {
                    var _a, _b;

                    if (!this.needsPlugin(options) && !this.needsPlugin(source)) {
                        return;
                    }

                    const optionsCast = options;

                    if (source === null || source === void 0 ? void 0 : source.emitters) {
                        if ((source === null || source === void 0 ? void 0 : source.emitters) instanceof Array) {
                            optionsCast.emitters = source === null || source === void 0 ? void 0 : source.emitters.map(s => {
                                const tmp = new Emitter();
                                tmp.load(s);
                                return tmp;
                            });
                        } else {
                            let emitterOptions = optionsCast.emitters;

                            if ((emitterOptions === null || emitterOptions === void 0 ? void 0 : emitterOptions.load) === undefined) {
                                optionsCast.emitters = emitterOptions = new Emitter();
                            }

                            emitterOptions.load(source === null || source === void 0 ? void 0 : source.emitters);
                        }
                    }

                    const interactivityEmitters = (_b = (_a = source === null || source === void 0 ? void 0 : source.interactivity) === null || _a === void 0 ? void 0 : _a.modes) === null || _b === void 0 ? void 0 : _b.emitters;

                    if (interactivityEmitters) {
                        if (interactivityEmitters instanceof Array) {
                            optionsCast.interactivity.modes.emitters = interactivityEmitters.map(s => {
                                const tmp = new Emitter();
                                tmp.load(s);
                                return tmp;
                            });
                        } else {
                            let emitterOptions = optionsCast.interactivity.modes.emitters;

                            if ((emitterOptions === null || emitterOptions === void 0 ? void 0 : emitterOptions.load) === undefined) {
                                optionsCast.interactivity.modes.emitters = emitterOptions = new Emitter();
                            }

                            emitterOptions.load(interactivityEmitters);
                        }
                    }
                }

            }

            function loadEmittersPlugin(tsParticles) {
                const plugin = new EmittersPlugin();
                tsParticles.addPlugin(plugin);
            }
            ;// CONCATENATED MODULE: ./dist/browser/Plugins/PolygonMask/Enums/InlineArrangement.js
            var InlineArrangement;

            (function (InlineArrangement) {
                InlineArrangement["equidistant"] = "equidistant";
                InlineArrangement["onePerPoint"] = "one-per-point";
                InlineArrangement["perPoint"] = "per-point";
                InlineArrangement["randomLength"] = "random-length";
                InlineArrangement["randomPoint"] = "random-point";
            })(InlineArrangement || (InlineArrangement = {}));
            ;// CONCATENATED MODULE: ./dist/browser/Plugins/PolygonMask/Enums/MoveType.js
            var MoveType;

            (function (MoveType) {
                MoveType["path"] = "path";
                MoveType["radius"] = "radius";
            })(MoveType || (MoveType = {}));
            ;// CONCATENATED MODULE: ./dist/browser/Plugins/PolygonMask/Enums/Type.js
            var Type;

            (function (Type) {
                Type["inline"] = "inline";
                Type["inside"] = "inside";
                Type["outside"] = "outside";
                Type["none"] = "none";
            })(Type || (Type = {}));
            ;// CONCATENATED MODULE: ./dist/browser/Plugins/PolygonMask/Enums/index.js



            ;// CONCATENATED MODULE: ./dist/browser/Plugins/PolygonMask/Options/Classes/DrawStroke.js


            class DrawStroke {
                constructor() {
                    this.color = new OptionsColor();
                    this.width = 0.5;
                    this.opacity = 1;
                }

                load(data) {
                    var _a;

                    if (data !== undefined) {
                        this.color = OptionsColor.create(this.color, data.color);

                        if (typeof this.color.value === "string") {
                            this.opacity = (_a = stringToAlpha(this.color.value)) !== null && _a !== void 0 ? _a : this.opacity;
                        }

                        if (data.opacity !== undefined) {
                            this.opacity = data.opacity;
                        }

                        if (data.width !== undefined) {
                            this.width = data.width;
                        }
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Plugins/PolygonMask/Options/Classes/Draw.js


            class Draw {
                constructor() {
                    this.enable = false;
                    this.stroke = new DrawStroke();
                }

                get lineWidth() {
                    return this.stroke.width;
                }

                set lineWidth(value) {
                    this.stroke.width = value;
                }

                get lineColor() {
                    return this.stroke.color;
                }

                set lineColor(value) {
                    this.stroke.color = OptionsColor.create(this.stroke.color, value);
                }

                load(data) {
                    var _a;

                    if (data !== undefined) {
                        if (data.enable !== undefined) {
                            this.enable = data.enable;
                        }

                        const stroke = (_a = data.stroke) !== null && _a !== void 0 ? _a : {
                            color: data.lineColor,
                            width: data.lineWidth
                        };
                        this.stroke.load(stroke);
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Plugins/PolygonMask/Options/Classes/Move.js

            class Move_Move {
                constructor() {
                    this.radius = 10;
                    this.type = MoveType.path;
                }

                load(data) {
                    if (data !== undefined) {
                        if (data.radius !== undefined) {
                            this.radius = data.radius;
                        }

                        if (data.type !== undefined) {
                            this.type = data.type;
                        }
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Plugins/PolygonMask/Options/Classes/Inline.js

            class Inline {
                constructor() {
                    this.arrangement = InlineArrangement.onePerPoint;
                }

                load(data) {
                    if (data !== undefined) {
                        if (data.arrangement !== undefined) {
                            this.arrangement = data.arrangement;
                        }
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Plugins/PolygonMask/Options/Classes/LocalSvg.js
            class LocalSvg {
                constructor() {
                    this.path = [];
                    this.size = {
                        height: 0,
                        width: 0
                    };
                }

                load(data) {
                    if (data !== undefined) {
                        if (data.path !== undefined) {
                            this.path = data.path;
                        }

                        if (data.size !== undefined) {
                            if (data.size.width !== undefined) {
                                this.size.width = data.size.width;
                            }

                            if (data.size.height !== undefined) {
                                this.size.height = data.size.height;
                            }
                        }
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Plugins/PolygonMask/Options/Classes/PolygonMask.js






            class PolygonMask {
                constructor() {
                    this.draw = new Draw();
                    this.enable = false;
                    this.inline = new Inline();
                    this.move = new Move_Move();
                    this.scale = 1;
                    this.type = Type.none;
                }

                get inlineArrangement() {
                    return this.inline.arrangement;
                }

                set inlineArrangement(value) {
                    this.inline.arrangement = value;
                }

                load(data) {
                    var _a;

                    if (data !== undefined) {
                        this.draw.load(data.draw);
                        const inline = (_a = data.inline) !== null && _a !== void 0 ? _a : {
                            arrangement: data.inlineArrangement
                        };

                        if (inline !== undefined) {
                            this.inline.load(inline);
                        }

                        this.move.load(data.move);

                        if (data.scale !== undefined) {
                            this.scale = data.scale;
                        }

                        if (data.type !== undefined) {
                            this.type = data.type;
                        }

                        if (data.enable !== undefined) {
                            this.enable = data.enable;
                        } else {
                            this.enable = this.type !== Type.none;
                        }

                        if (data.url !== undefined) {
                            this.url = data.url;
                        }

                        if (data.data !== undefined) {
                            if (typeof data.data === "string") {
                                this.data = data.data;
                            } else {
                                this.data = new LocalSvg();
                                this.data.load(data.data);
                            }
                        }

                        if (data.position !== undefined) {
                            this.position = deepExtend({}, data.position);
                        }
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Plugins/PolygonMask/PolygonMaskInstance.js
            var PolygonMaskInstance_awaiter = undefined && undefined.__awaiter || function (thisArg, _arguments, P, generator) {
                function adopt(value) {
                    return value instanceof P ? value : new P(function (resolve) {
                        resolve(value);
                    });
                }

                return new (P || (P = Promise))(function (resolve, reject) {
                    function fulfilled(value) {
                        try {
                            step(generator.next(value));
                        } catch (e) {
                            reject(e);
                        }
                    }

                    function rejected(value) {
                        try {
                            step(generator["throw"](value));
                        } catch (e) {
                            reject(e);
                        }
                    }

                    function step(result) {
                        result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected);
                    }

                    step((generator = generator.apply(thisArg, _arguments || [])).next());
                });
            };






            function drawPolygonMask(context, rawData, stroke) {
                const color = colorToRgb(stroke.color);

                if (!color) {
                    return;
                }

                context.beginPath();
                context.moveTo(rawData[0].x, rawData[0].y);

                for (const item of rawData) {
                    context.lineTo(item.x, item.y);
                }

                context.closePath();
                context.strokeStyle = getStyleFromRgb(color);
                context.lineWidth = stroke.width;
                context.stroke();
            }

            function drawPolygonMaskPath(context, path, stroke, position) {
                context.translate(position.x, position.y);
                const color = colorToRgb(stroke.color);

                if (!color) {
                    return;
                }

                context.strokeStyle = getStyleFromRgb(color, stroke.opacity);
                context.lineWidth = stroke.width;
                context.stroke(path);
            }

            function parsePaths(paths, scale, offset) {
                const res = [];

                for (const path of paths) {
                    const segments = path.element.pathSegList;
                    const len = segments.numberOfItems;
                    const p = {
                        x: 0,
                        y: 0
                    };

                    for (let i = 0; i < len; i++) {
                        const segment = segments.getItem(i);
                        const svgPathSeg = window.SVGPathSeg;

                        switch (segment.pathSegType) {
                            case svgPathSeg.PATHSEG_MOVETO_ABS:
                            case svgPathSeg.PATHSEG_LINETO_ABS:
                            case svgPathSeg.PATHSEG_CURVETO_CUBIC_ABS:
                            case svgPathSeg.PATHSEG_CURVETO_QUADRATIC_ABS:
                            case svgPathSeg.PATHSEG_ARC_ABS:
                            case svgPathSeg.PATHSEG_CURVETO_CUBIC_SMOOTH_ABS:
                            case svgPathSeg.PATHSEG_CURVETO_QUADRATIC_SMOOTH_ABS:
                                {
                                    const absSeg = segment;
                                    p.x = absSeg.x;
                                    p.y = absSeg.y;
                                    break;
                                }

                            case svgPathSeg.PATHSEG_LINETO_HORIZONTAL_ABS:
                                p.x = segment.x;
                                break;

                            case svgPathSeg.PATHSEG_LINETO_VERTICAL_ABS:
                                p.y = segment.y;
                                break;

                            case svgPathSeg.PATHSEG_LINETO_REL:
                            case svgPathSeg.PATHSEG_MOVETO_REL:
                            case svgPathSeg.PATHSEG_CURVETO_CUBIC_REL:
                            case svgPathSeg.PATHSEG_CURVETO_QUADRATIC_REL:
                            case svgPathSeg.PATHSEG_ARC_REL:
                            case svgPathSeg.PATHSEG_CURVETO_CUBIC_SMOOTH_REL:
                            case svgPathSeg.PATHSEG_CURVETO_QUADRATIC_SMOOTH_REL:
                                {
                                    const relSeg = segment;
                                    p.x += relSeg.x;
                                    p.y += relSeg.y;
                                    break;
                                }

                            case svgPathSeg.PATHSEG_LINETO_HORIZONTAL_REL:
                                p.x += segment.x;
                                break;

                            case svgPathSeg.PATHSEG_LINETO_VERTICAL_REL:
                                p.y += segment.y;
                                break;

                            case svgPathSeg.PATHSEG_UNKNOWN:
                            case svgPathSeg.PATHSEG_CLOSEPATH:
                                continue;
                        }

                        res.push({
                            x: p.x * scale + offset.x,
                            y: p.y * scale + offset.y
                        });
                    }
                }

                return res;
            }

            function calcClosestPtOnSegment(s1, s2, pos) {
                const {
                    dx,
                    dy
                } = getDistances(pos, s1);
                const {
                    dx: dxx,
                    dy: dyy
                } = getDistances(s2, s1);
                const t = (dx * dxx + dy * dyy) / (Math.pow(dxx, 2) + Math.pow(dyy, 2));
                let x = s1.x + dxx * t;
                let y = s1.y + dyy * t;

                if (t < 0) {
                    x = s1.x;
                    y = s1.y;
                } else if (t > 1) {
                    x = s2.x;
                    y = s2.y;
                }

                return {
                    x: x,
                    y: y,
                    isOnSegment: t >= 0 && t <= 1
                };
            }

            function segmentBounce(start, stop, velocity) {
                const {
                    dx,
                    dy
                } = getDistances(start, stop);
                const wallAngle = Math.atan2(dy, dx);
                const wallNormalX = Math.sin(wallAngle);
                const wallNormalY = -Math.cos(wallAngle);
                const d = 2 * (velocity.x * wallNormalX + velocity.y * wallNormalY);
                velocity.x -= d * wallNormalX;
                velocity.y -= d * wallNormalY;
            }

            class PolygonMaskInstance {
                constructor(container) {
                    this.container = container;
                    this.dimension = {
                        height: 0,
                        width: 0
                    };
                    this.path2DSupported = !!window.Path2D;
                    this.options = new PolygonMask();
                    this.polygonMaskMoveRadius = this.options.move.radius * container.retina.pixelRatio;
                }

                initAsync(options) {
                    return PolygonMaskInstance_awaiter(this, void 0, void 0, function* () {
                        this.options.load(options === null || options === void 0 ? void 0 : options.polygon);
                        const polygonMaskOptions = this.options;
                        this.polygonMaskMoveRadius = polygonMaskOptions.move.radius * this.container.retina.pixelRatio;

                        if (polygonMaskOptions.enable) {
                            yield this.initRawData();
                        }
                    });
                }

                resize() {
                    const container = this.container;
                    const options = this.options;

                    if (!(options.enable && options.type !== Type.none)) {
                        return;
                    }

                    if (this.redrawTimeout) {
                        clearTimeout(this.redrawTimeout);
                    }

                    this.redrawTimeout = window.setTimeout(() => PolygonMaskInstance_awaiter(this, void 0, void 0, function* () {
                        yield this.initRawData(true);
                        container.particles.redraw();
                    }), 250);
                }

                stop() {
                    delete this.raw;
                    delete this.paths;
                }

                particlesInitialization() {
                    const options = this.options;

                    if (options.enable && options.type === Type.inline && (options.inline.arrangement === InlineArrangement.onePerPoint || options.inline.arrangement === InlineArrangement.perPoint)) {
                        this.drawPoints();
                        return true;
                    }

                    return false;
                }

                particlePosition(position) {
                    var _a, _b;

                    const options = this.options;

                    if (!(options.enable && ((_b = (_a = this.raw) === null || _a === void 0 ? void 0 : _a.length) !== null && _b !== void 0 ? _b : 0) > 0)) {
                        return;
                    }

                    return deepExtend({}, position ? position : this.randomPoint());
                }

                particleBounce(particle, delta, direction) {
                    return this.polygonBounce(particle, delta, direction);
                }

                clickPositionValid(position) {
                    const options = this.options;
                    return options.enable && options.type !== Type.none && options.type !== Type.inline && this.checkInsidePolygon(position);
                }

                draw(context) {
                    var _a;

                    if (!((_a = this.paths) === null || _a === void 0 ? void 0 : _a.length)) {
                        return;
                    }

                    const options = this.options;
                    const polygonDraw = options.draw;

                    if (!(options.enable && polygonDraw.enable)) {
                        return;
                    }

                    const rawData = this.raw;

                    for (const path of this.paths) {
                        const path2d = path.path2d;
                        const path2dSupported = this.path2DSupported;

                        if (!context) {
                            continue;
                        }

                        if (path2dSupported && path2d && this.offset) {
                            drawPolygonMaskPath(context, path2d, polygonDraw.stroke, this.offset);
                        } else if (rawData) {
                            drawPolygonMask(context, rawData, polygonDraw.stroke);
                        }
                    }
                }

                polygonBounce(particle, delta, direction) {
                    const options = this.options;

                    if (!this.raw || !options.enable || direction !== OutModeDirection.top) {
                        return false;
                    }

                    if (options.type === Type.inside || options.type === Type.outside) {
                        let closest, dx, dy;
                        const pos = particle.getPosition(),
                            radius = particle.getRadius();

                        for (let i = 0, j = this.raw.length - 1; i < this.raw.length; j = i++) {
                            const pi = this.raw[i],
                                pj = this.raw[j];
                            closest = calcClosestPtOnSegment(pi, pj, pos);
                            const dist = getDistances(pos, closest);
                            [dx, dy] = [dist.dx, dist.dy];

                            if (dist.distance < radius) {
                                segmentBounce(pi, pj, particle.velocity);
                                return true;
                            }
                        }

                        if (closest && dx !== undefined && dy !== undefined && !this.checkInsidePolygon(pos)) {
                            const factor = {
                                x: 1,
                                y: 1
                            };

                            if (particle.position.x >= closest.x) {
                                factor.x = -1;
                            }

                            if (particle.position.y >= closest.y) {
                                factor.y = -1;
                            }

                            particle.position.x = closest.x + radius * 2 * factor.x;
                            particle.position.y = closest.y + radius * 2 * factor.y;
                            particle.velocity.mult(-1);
                            return true;
                        }
                    } else if (options.type === Type.inline && particle.initialPosition) {
                        const dist = getDistance(particle.initialPosition, particle.getPosition());

                        if (dist > this.polygonMaskMoveRadius) {
                            particle.velocity.x = particle.velocity.y / 2 - particle.velocity.x;
                            particle.velocity.y = particle.velocity.x / 2 - particle.velocity.y;
                            return true;
                        }
                    }

                    return false;
                }

                checkInsidePolygon(position) {
                    var _a, _b;

                    const container = this.container;
                    const options = this.options;

                    if (!options.enable || options.type === Type.none || options.type === Type.inline) {
                        return true;
                    }

                    if (!this.raw) {
                        throw new Error(Constants.noPolygonFound);
                    }

                    const canvasSize = container.canvas.size;
                    const x = (_a = position === null || position === void 0 ? void 0 : position.x) !== null && _a !== void 0 ? _a : Math.random() * canvasSize.width;
                    const y = (_b = position === null || position === void 0 ? void 0 : position.y) !== null && _b !== void 0 ? _b : Math.random() * canvasSize.height;
                    let inside = false;

                    for (let i = 0, j = this.raw.length - 1; i < this.raw.length; j = i++) {
                        const pi = this.raw[i];
                        const pj = this.raw[j];
                        const intersect = pi.y > y !== pj.y > y && x < (pj.x - pi.x) * (y - pi.y) / (pj.y - pi.y) + pi.x;

                        if (intersect) {
                            inside = !inside;
                        }
                    }

                    return options.type === Type.inside ? inside : options.type === Type.outside ? !inside : false;
                }

                parseSvgPath(xml, force) {
                    var _a, _b, _c;

                    const forceDownload = force !== null && force !== void 0 ? force : false;

                    if (this.paths !== undefined && !forceDownload) {
                        return this.raw;
                    }

                    const container = this.container;
                    const options = this.options;
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(xml, "image/svg+xml");
                    const svg = doc.getElementsByTagName("svg")[0];
                    let svgPaths = svg.getElementsByTagName("path");

                    if (!svgPaths.length) {
                        svgPaths = doc.getElementsByTagName("path");
                    }

                    this.paths = [];

                    for (let i = 0; i < svgPaths.length; i++) {
                        const path = svgPaths.item(i);

                        if (path) {
                            this.paths.push({
                                element: path,
                                length: path.getTotalLength()
                            });
                        }
                    }

                    const pxRatio = container.retina.pixelRatio;
                    const scale = options.scale / pxRatio;
                    this.dimension.width = parseFloat((_a = svg.getAttribute("width")) !== null && _a !== void 0 ? _a : "0") * scale;
                    this.dimension.height = parseFloat((_b = svg.getAttribute("height")) !== null && _b !== void 0 ? _b : "0") * scale;
                    const position = (_c = options.position) !== null && _c !== void 0 ? _c : {
                        x: 50,
                        y: 50
                    };
                    this.offset = {
                        x: container.canvas.size.width * position.x / (100 * pxRatio) - this.dimension.width / 2,
                        y: container.canvas.size.height * position.y / (100 * pxRatio) - this.dimension.height / 2
                    };
                    return parsePaths(this.paths, scale, this.offset);
                }

                downloadSvgPath(svgUrl, force) {
                    return PolygonMaskInstance_awaiter(this, void 0, void 0, function* () {
                        const options = this.options;
                        const url = svgUrl || options.url;
                        const forceDownload = force !== null && force !== void 0 ? force : false;

                        if (!url || this.paths !== undefined && !forceDownload) {
                            return this.raw;
                        }

                        const req = yield fetch(url);

                        if (!req.ok) {
                            throw new Error("tsParticles Error - Error occurred during polygon mask download");
                        }

                        return this.parseSvgPath(yield req.text(), force);
                    });
                }

                drawPoints() {
                    if (!this.raw) {
                        return;
                    }

                    for (const item of this.raw) {
                        this.container.particles.addParticle({
                            x: item.x,
                            y: item.y
                        });
                    }
                }

                randomPoint() {
                    const container = this.container;
                    const options = this.options;
                    let position;

                    if (options.type === Type.inline) {
                        switch (options.inline.arrangement) {
                            case InlineArrangement.randomPoint:
                                position = this.getRandomPoint();
                                break;

                            case InlineArrangement.randomLength:
                                position = this.getRandomPointByLength();
                                break;

                            case InlineArrangement.equidistant:
                                position = this.getEquidistantPointByIndex(container.particles.count);
                                break;

                            case InlineArrangement.onePerPoint:
                            case InlineArrangement.perPoint:
                            default:
                                position = this.getPointByIndex(container.particles.count);
                        }
                    } else {
                        position = {
                            x: Math.random() * container.canvas.size.width,
                            y: Math.random() * container.canvas.size.height
                        };
                    }

                    if (this.checkInsidePolygon(position)) {
                        return position;
                    } else {
                        return this.randomPoint();
                    }
                }

                getRandomPoint() {
                    if (!this.raw || !this.raw.length) {
                        throw new Error(Constants.noPolygonDataLoaded);
                    }

                    const coords = itemFromArray(this.raw);
                    return {
                        x: coords.x,
                        y: coords.y
                    };
                }

                getRandomPointByLength() {
                    var _a, _b, _c;

                    const options = this.options;

                    if (!this.raw || !this.raw.length || !((_a = this.paths) === null || _a === void 0 ? void 0 : _a.length)) {
                        throw new Error(Constants.noPolygonDataLoaded);
                    }

                    const path = itemFromArray(this.paths);
                    const distance = Math.floor(Math.random() * path.length) + 1;
                    const point = path.element.getPointAtLength(distance);
                    return {
                        x: point.x * options.scale + (((_b = this.offset) === null || _b === void 0 ? void 0 : _b.x) || 0),
                        y: point.y * options.scale + (((_c = this.offset) === null || _c === void 0 ? void 0 : _c.y) || 0)
                    };
                }

                getEquidistantPointByIndex(index) {
                    var _a, _b, _c, _d, _e, _f, _g;

                    const options = this.container.actualOptions;
                    const polygonMaskOptions = this.options;
                    if (!this.raw || !this.raw.length || !((_a = this.paths) === null || _a === void 0 ? void 0 : _a.length)) throw new Error(Constants.noPolygonDataLoaded);
                    let offset = 0;
                    let point;
                    const totalLength = this.paths.reduce((tot, path) => tot + path.length, 0);
                    const distance = totalLength / options.particles.number.value;

                    for (const path of this.paths) {
                        const pathDistance = distance * index - offset;

                        if (pathDistance <= path.length) {
                            point = path.element.getPointAtLength(pathDistance);
                            break;
                        } else {
                            offset += path.length;
                        }
                    }

                    return {
                        x: ((_b = point === null || point === void 0 ? void 0 : point.x) !== null && _b !== void 0 ? _b : 0) * polygonMaskOptions.scale + ((_d = (_c = this.offset) === null || _c === void 0 ? void 0 : _c.x) !== null && _d !== void 0 ? _d : 0),
                        y: ((_e = point === null || point === void 0 ? void 0 : point.y) !== null && _e !== void 0 ? _e : 0) * polygonMaskOptions.scale + ((_g = (_f = this.offset) === null || _f === void 0 ? void 0 : _f.y) !== null && _g !== void 0 ? _g : 0)
                    };
                }

                getPointByIndex(index) {
                    if (!this.raw || !this.raw.length) {
                        throw new Error(Constants.noPolygonDataLoaded);
                    }

                    const coords = this.raw[index % this.raw.length];
                    return {
                        x: coords.x,
                        y: coords.y
                    };
                }

                createPath2D() {
                    var _a, _b;

                    const options = this.options;

                    if (!this.path2DSupported || !((_a = this.paths) === null || _a === void 0 ? void 0 : _a.length)) {
                        return;
                    }

                    for (const path of this.paths) {
                        const pathData = (_b = path.element) === null || _b === void 0 ? void 0 : _b.getAttribute("d");

                        if (pathData) {
                            const path2d = new Path2D(pathData);
                            const matrix = document.createElementNS("http://www.w3.org/2000/svg", "svg").createSVGMatrix();
                            const finalPath = new Path2D();
                            const transform = matrix.scale(options.scale);

                            if (finalPath.addPath) {
                                finalPath.addPath(path2d, transform);
                                path.path2d = finalPath;
                            } else {
                                delete path.path2d;
                            }
                        } else {
                            delete path.path2d;
                        }

                        if (path.path2d || !this.raw) {
                            continue;
                        }

                        path.path2d = new Path2D();
                        path.path2d.moveTo(this.raw[0].x, this.raw[0].y);
                        this.raw.forEach((pos, i) => {
                            var _a;

                            if (i > 0) {
                                (_a = path.path2d) === null || _a === void 0 ? void 0 : _a.lineTo(pos.x, pos.y);
                            }
                        });
                        path.path2d.closePath();
                    }
                }

                initRawData(force) {
                    return PolygonMaskInstance_awaiter(this, void 0, void 0, function* () {
                        const options = this.options;

                        if (options.url) {
                            this.raw = yield this.downloadSvgPath(options.url, force);
                        } else if (options.data) {
                            const data = options.data;
                            let svg;

                            if (typeof data !== "string") {
                                const path = data.path instanceof Array ? data.path.map(t => `<path d="${t}" />`).join("") : `<path d="${data.path}" />`;
                                const namespaces = 'xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"';
                                svg = `<svg ${namespaces} width="${data.size.width}" height="${data.size.height}">${path}</svg>`;
                            } else {
                                svg = data;
                            }

                            this.raw = this.parseSvgPath(svg, force);
                        }

                        this.createPath2D();
                    });
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Plugins/PolygonMask/plugin.js




            class plugin_Plugin {
                constructor() {
                    this.id = "polygonMask";
                }

                getPlugin(container) {
                    return new PolygonMaskInstance(container);
                }

                needsPlugin(options) {
                    var _a, _b, _c;

                    return (_b = (_a = options === null || options === void 0 ? void 0 : options.polygon) === null || _a === void 0 ? void 0 : _a.enable) !== null && _b !== void 0 ? _b : ((_c = options === null || options === void 0 ? void 0 : options.polygon) === null || _c === void 0 ? void 0 : _c.type) !== undefined && options.polygon.type !== Type.none;
                }

                loadOptions(options, source) {
                    if (!this.needsPlugin(source)) {
                        return;
                    }

                    const optionsCast = options;
                    let polygonOptions = optionsCast.polygon;

                    if ((polygonOptions === null || polygonOptions === void 0 ? void 0 : polygonOptions.load) === undefined) {
                        optionsCast.polygon = polygonOptions = new PolygonMask();
                    }

                    polygonOptions.load(source === null || source === void 0 ? void 0 : source.polygon);
                }

            }

            function loadPolygonMaskPlugin(tsParticles) {
                const plugin = new plugin_Plugin();
                tsParticles.addPlugin(plugin);
            }
            ;// CONCATENATED MODULE: ./dist/browser/Shapes/Circle/CircleDrawer.js
            class CircleDrawer {
                getSidesCount() {
                    return 12;
                }

                draw(context, particle, radius) {
                    context.arc(0, 0, radius, 0, Math.PI * 2, false);
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Shapes/Circle/index.js

            function loadCircleShape(tsParticles) {
                tsParticles.addShape("circle", new CircleDrawer());
            }
            ;// CONCATENATED MODULE: ./dist/browser/Shapes/Image/Utils.js
            var Utils_awaiter = undefined && undefined.__awaiter || function (thisArg, _arguments, P, generator) {
                function adopt(value) {
                    return value instanceof P ? value : new P(function (resolve) {
                        resolve(value);
                    });
                }

                return new (P || (P = Promise))(function (resolve, reject) {
                    function fulfilled(value) {
                        try {
                            step(generator.next(value));
                        } catch (e) {
                            reject(e);
                        }
                    }

                    function rejected(value) {
                        try {
                            step(generator["throw"](value));
                        } catch (e) {
                            reject(e);
                        }
                    }

                    function step(result) {
                        result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected);
                    }

                    step((generator = generator.apply(thisArg, _arguments || [])).next());
                });
            };


            function loadImage(source) {
                return new Promise((resolve, reject) => {
                    if (!source) {
                        reject("Error tsParticles - No image.src");
                        return;
                    }

                    const image = {
                        source: source,
                        type: source.substr(source.length - 3)
                    };
                    const img = new Image();
                    img.addEventListener("load", () => {
                        image.element = img;
                        resolve(image);
                    });
                    img.addEventListener("error", () => {
                        reject(`Error tsParticles - loading image: ${source}`);
                    });
                    img.src = source;
                });
            }
            function downloadSvgImage(source) {
                return Utils_awaiter(this, void 0, void 0, function* () {
                    if (!source) {
                        throw new Error("Error tsParticles - No image.src");
                    }

                    const image = {
                        source: source,
                        type: source.substr(source.length - 3)
                    };

                    if (image.type !== "svg") {
                        return loadImage(source);
                    }

                    const response = yield fetch(image.source);

                    if (!response.ok) {
                        throw new Error("Error tsParticles - Image not found");
                    }

                    image.svgData = yield response.text();
                    return image;
                });
            }
            function replaceColorSvg(imageShape, color, opacity) {
                const {
                    svgData
                } = imageShape;

                if (!svgData) {
                    return "";
                }

                if (svgData.includes("fill")) {
                    const currentColor = /(#(?:[0-9a-f]{2}){2,4}|(#[0-9a-f]{3})|(rgb|hsl)a?\((-?\d+%?[,\s]+){2,3}\s*[\d.]+%?\))|currentcolor/gi;
                    return svgData.replace(currentColor, () => getStyleFromHsl(color, opacity));
                }

                const preFillIndex = svgData.indexOf(">");
                return `${svgData.substring(0, preFillIndex)} fill="${getStyleFromHsl(color, opacity)}"${svgData.substring(preFillIndex)}`;
            }
            ;// CONCATENATED MODULE: ./dist/browser/Shapes/Image/ImageDrawer.js
            var ImageDrawer_awaiter = undefined && undefined.__awaiter || function (thisArg, _arguments, P, generator) {
                function adopt(value) {
                    return value instanceof P ? value : new P(function (resolve) {
                        resolve(value);
                    });
                }

                return new (P || (P = Promise))(function (resolve, reject) {
                    function fulfilled(value) {
                        try {
                            step(generator.next(value));
                        } catch (e) {
                            reject(e);
                        }
                    }

                    function rejected(value) {
                        try {
                            step(generator["throw"](value));
                        } catch (e) {
                            reject(e);
                        }
                    }

                    function step(result) {
                        result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected);
                    }

                    step((generator = generator.apply(thisArg, _arguments || [])).next());
                });
            };

            var ImageDrawer_classPrivateFieldSet = undefined && undefined.__classPrivateFieldSet || function (receiver, state, value, kind, f) {
                if (kind === "m") throw new TypeError("Private method is not writable");
                if (kind === "a" && !f) throw new TypeError("Private accessor was defined without a setter");
                if (typeof state === "function" ? receiver !== state || !f : !state.has(receiver)) throw new TypeError("Cannot write private member to an object whose class did not declare it");
                return kind === "a" ? f.call(receiver, value) : f ? f.value = value : state.set(receiver, value), value;
            };

            var ImageDrawer_classPrivateFieldGet = undefined && undefined.__classPrivateFieldGet || function (receiver, state, kind, f) {
                if (kind === "a" && !f) throw new TypeError("Private accessor was defined without a getter");
                if (typeof state === "function" ? receiver !== state || !f : !state.has(receiver)) throw new TypeError("Cannot read private member from an object whose class did not declare it");
                return kind === "m" ? f : kind === "a" ? f.call(receiver) : f ? f.value : state.get(receiver);
            };

            var _ImageDrawer_images;




            class ImageDrawer {
                constructor() {
                    _ImageDrawer_images.set(this, void 0);

                    ImageDrawer_classPrivateFieldSet(this, _ImageDrawer_images, [], "f");
                }

                getSidesCount() {
                    return 12;
                }

                getImages(container) {
                    const containerImages = ImageDrawer_classPrivateFieldGet(this, _ImageDrawer_images, "f").find(t => t.id === container.id);

                    if (!containerImages) {
                        ImageDrawer_classPrivateFieldGet(this, _ImageDrawer_images, "f").push({
                            id: container.id,
                            images: []
                        });

                        return this.getImages(container);
                    } else {
                        return containerImages;
                    }
                }

                addImage(container, image) {
                    const containerImages = this.getImages(container);
                    containerImages === null || containerImages === void 0 ? void 0 : containerImages.images.push(image);
                }

                init(container) {
                    return ImageDrawer_awaiter(this, void 0, void 0, function* () {
                        yield this.loadImagesFromParticlesOptions(container, container.actualOptions.particles);
                        yield this.loadImagesFromParticlesOptions(container, container.actualOptions.interactivity.modes.trail.particles);

                        for (const manualParticle of container.actualOptions.manualParticles) {
                            yield this.loadImagesFromParticlesOptions(container, manualParticle.options);
                        }

                        const emitterOptions = container.actualOptions;

                        if (emitterOptions.emitters) {
                            if (emitterOptions.emitters instanceof Array) {
                                for (const emitter of emitterOptions.emitters) {
                                    yield this.loadImagesFromParticlesOptions(container, emitter.particles);
                                }
                            } else {
                                yield this.loadImagesFromParticlesOptions(container, emitterOptions.emitters.particles);
                            }
                        }

                        const interactiveEmitters = emitterOptions.interactivity.modes.emitters;

                        if (interactiveEmitters) {
                            if (interactiveEmitters instanceof Array) {
                                for (const emitter of interactiveEmitters) {
                                    yield this.loadImagesFromParticlesOptions(container, emitter.particles);
                                }
                            } else {
                                yield this.loadImagesFromParticlesOptions(container, interactiveEmitters.particles);
                            }
                        }
                    });
                }

                destroy() {
                    ImageDrawer_classPrivateFieldSet(this, _ImageDrawer_images, [], "f");
                }

                loadImagesFromParticlesOptions(container, options) {
                    var _a, _b, _c;

                    return ImageDrawer_awaiter(this, void 0, void 0, function* () {
                        const shapeOptions = options === null || options === void 0 ? void 0 : options.shape;

                        if (!(shapeOptions === null || shapeOptions === void 0 ? void 0 : shapeOptions.type) || !shapeOptions.options || !isInArray(ShapeType.image, shapeOptions.type) && !isInArray(ShapeType.images, shapeOptions.type)) {
                            return;
                        }

                        const idx = ImageDrawer_classPrivateFieldGet(this, _ImageDrawer_images, "f").findIndex(t => t.id === container.id);

                        if (idx >= 0) {
                            ImageDrawer_classPrivateFieldGet(this, _ImageDrawer_images, "f").splice(idx, 1);
                        }

                        const imageOptions = (_a = shapeOptions.options[ShapeType.images]) !== null && _a !== void 0 ? _a : shapeOptions.options[ShapeType.image];

                        if (imageOptions instanceof Array) {
                            for (const optionsImage of imageOptions) {
                                yield this.loadImageShape(container, optionsImage);
                            }
                        } else {
                            yield this.loadImageShape(container, imageOptions);
                        }

                        if (options === null || options === void 0 ? void 0 : options.groups) {
                            for (const groupName in options.groups) {
                                const group = options.groups[groupName];
                                yield this.loadImagesFromParticlesOptions(container, group);
                            }
                        }

                        if ((_c = (_b = options === null || options === void 0 ? void 0 : options.destroy) === null || _b === void 0 ? void 0 : _b.split) === null || _c === void 0 ? void 0 : _c.particles) {
                            yield this.loadImagesFromParticlesOptions(container, options === null || options === void 0 ? void 0 : options.destroy.split.particles);
                        }
                    });
                }

                loadImageShape(container, imageShape) {
                    return ImageDrawer_awaiter(this, void 0, void 0, function* () {
                        try {
                            const imageFunc = imageShape.replaceColor ? downloadSvgImage : loadImage;
                            const image = yield imageFunc(imageShape.src);

                            if (image) {
                                this.addImage(container, image);
                            }
                        } catch (_a) {
                            console.warn(`tsParticles error - ${imageShape.src} not found`);
                        }
                    });
                }

                draw(context, particle, radius, opacity) {
                    var _a, _b;

                    if (!context) {
                        return;
                    }

                    const image = particle.image;
                    const element = (_a = image === null || image === void 0 ? void 0 : image.data) === null || _a === void 0 ? void 0 : _a.element;

                    if (!element) {
                        return;
                    }

                    const ratio = (_b = image === null || image === void 0 ? void 0 : image.ratio) !== null && _b !== void 0 ? _b : 1;
                    const pos = {
                        x: -radius,
                        y: -radius
                    };

                    if (!(image === null || image === void 0 ? void 0 : image.data.svgData) || !(image === null || image === void 0 ? void 0 : image.replaceColor)) {
                        context.globalAlpha = opacity;
                    }

                    context.drawImage(element, pos.x, pos.y, radius * 2, radius * 2 / ratio);

                    if (!(image === null || image === void 0 ? void 0 : image.data.svgData) || !(image === null || image === void 0 ? void 0 : image.replaceColor)) {
                        context.globalAlpha = 1;
                    }
                }

                loadShape(particle) {
                    var _a, _b, _c, _d, _e;

                    if (particle.shape !== "image" && particle.shape !== "images") {
                        return;
                    }

                    const container = particle.container;
                    const images = this.getImages(container).images;
                    const imageData = particle.shapeData;
                    const image = (_a = images.find(t => t.source === imageData.src)) !== null && _a !== void 0 ? _a : images[0];
                    const color = particle.getFillColor();
                    let imageRes;

                    if (!image) {
                        return;
                    }

                    if (image.svgData !== undefined && imageData.replaceColor && color) {
                        const svgColoredData = replaceColorSvg(image, color, particle.opacity.value);
                        const svg = new Blob([svgColoredData], {
                            type: "image/svg+xml"
                        });
                        const domUrl = URL || window.URL || window.webkitURL || window;
                        const url = domUrl.createObjectURL(svg);
                        const img = new Image();
                        imageRes = {
                            data: Object.assign(Object.assign({}, image), {
                                svgData: svgColoredData
                            }),
                            ratio: imageData.width / imageData.height,
                            replaceColor: (_b = imageData.replaceColor) !== null && _b !== void 0 ? _b : imageData.replace_color,
                            source: imageData.src
                        };
                        img.addEventListener("load", () => {
                            const pImage = particle.image;

                            if (pImage) {
                                pImage.loaded = true;
                                image.element = img;
                            }

                            domUrl.revokeObjectURL(url);
                        });
                        img.addEventListener("error", () => {
                            domUrl.revokeObjectURL(url);
                            loadImage(imageData.src).then(img2 => {
                                const pImage = particle.image;

                                if (pImage) {
                                    image.element = img2 === null || img2 === void 0 ? void 0 : img2.element;
                                    pImage.loaded = true;
                                }
                            });
                        });
                        img.src = url;
                    } else {
                        imageRes = {
                            data: image,
                            loaded: true,
                            ratio: imageData.width / imageData.height,
                            replaceColor: (_c = imageData.replaceColor) !== null && _c !== void 0 ? _c : imageData.replace_color,
                            source: imageData.src
                        };
                    }

                    if (!imageRes.ratio) {
                        imageRes.ratio = 1;
                    }

                    const fill = (_d = imageData.fill) !== null && _d !== void 0 ? _d : particle.fill;
                    const close = (_e = imageData.close) !== null && _e !== void 0 ? _e : particle.close;
                    const imageShape = {
                        image: imageRes,
                        fill,
                        close
                    };
                    particle.image = imageShape.image;
                    particle.fill = imageShape.fill;
                    particle.close = imageShape.close;
                }

            }
            _ImageDrawer_images = new WeakMap();
            ;// CONCATENATED MODULE: ./dist/browser/Shapes/Image/index.js

            function loadImageShape(tsParticles) {
                const imageDrawer = new ImageDrawer();
                tsParticles.addShape("image", imageDrawer);
                tsParticles.addShape("images", imageDrawer);
            }
            ;// CONCATENATED MODULE: ./dist/browser/Shapes/Line/LineDrawer.js
            class LineDrawer {
                getSidesCount() {
                    return 1;
                }

                draw(context, particle, radius) {
                    context.moveTo(-radius / 2, 0);
                    context.lineTo(radius / 2, 0);
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Shapes/Line/index.js

            function loadLineShape(tsParticles) {
                tsParticles.addShape("line", new LineDrawer());
            }
            ;// CONCATENATED MODULE: ./dist/browser/Shapes/Polygon/PolygonDrawerBase.js
            class PolygonDrawerBase {
                getSidesCount(particle) {
                    var _a, _b;

                    const polygon = particle.shapeData;
                    return (_b = (_a = polygon === null || polygon === void 0 ? void 0 : polygon.sides) !== null && _a !== void 0 ? _a : polygon === null || polygon === void 0 ? void 0 : polygon.nb_sides) !== null && _b !== void 0 ? _b : 5;
                }

                draw(context, particle, radius) {
                    const start = this.getCenter(particle, radius);
                    const side = this.getSidesData(particle, radius);
                    const sideCount = side.count.numerator * side.count.denominator;
                    const decimalSides = side.count.numerator / side.count.denominator;
                    const interiorAngleDegrees = 180 * (decimalSides - 2) / decimalSides;
                    const interiorAngle = Math.PI - Math.PI * interiorAngleDegrees / 180;

                    if (!context) {
                        return;
                    }

                    context.beginPath();
                    context.translate(start.x, start.y);
                    context.moveTo(0, 0);

                    for (let i = 0; i < sideCount; i++) {
                        context.lineTo(side.length, 0);
                        context.translate(side.length, 0);
                        context.rotate(interiorAngle);
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Shapes/Polygon/PolygonDrawer.js

            class PolygonDrawer extends PolygonDrawerBase {
                getSidesData(particle, radius) {
                    var _a, _b;

                    const polygon = particle.shapeData;
                    const sides = (_b = (_a = polygon === null || polygon === void 0 ? void 0 : polygon.sides) !== null && _a !== void 0 ? _a : polygon === null || polygon === void 0 ? void 0 : polygon.nb_sides) !== null && _b !== void 0 ? _b : 5;
                    return {
                        count: {
                            denominator: 1,
                            numerator: sides
                        },
                        length: radius * 2.66 / (sides / 3)
                    };
                }

                getCenter(particle, radius) {
                    const sides = this.getSidesCount(particle);
                    return {
                        x: -radius / (sides / 3.5),
                        y: -radius / (2.66 / 3.5)
                    };
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Shapes/Polygon/TriangleDrawer.js

            class TriangleDrawer extends PolygonDrawerBase {
                getSidesCount() {
                    return 3;
                }

                getSidesData(particle, radius) {
                    return {
                        count: {
                            denominator: 2,
                            numerator: 3
                        },
                        length: radius * 2
                    };
                }

                getCenter(particle, radius) {
                    return {
                        x: -radius,
                        y: radius / 1.66
                    };
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Shapes/Polygon/index.js


            function loadGenericPolygonShape(tsParticles) {
                tsParticles.addShape("polygon", new PolygonDrawer());
            }
            function loadTriangleShape(tsParticles) {
                tsParticles.addShape("triangle", new TriangleDrawer());
            }
            function loadPolygonShape(tsParticles) {
                loadGenericPolygonShape(tsParticles);
                loadTriangleShape(tsParticles);
            }
            ;// CONCATENATED MODULE: ./dist/browser/Shapes/Square/SquareDrawer.js
            const fixFactor = Math.sqrt(2);
            class SquareDrawer {
                getSidesCount() {
                    return 4;
                }

                draw(context, particle, radius) {
                    context.rect(-radius / fixFactor, -radius / fixFactor, radius * 2 / fixFactor, radius * 2 / fixFactor);
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Shapes/Square/index.js

            function loadSquareShape(tsParticles) {
                const drawer = new SquareDrawer();
                tsParticles.addShape("edge", drawer);
                tsParticles.addShape("square", drawer);
            }
            ;// CONCATENATED MODULE: ./dist/browser/Shapes/Star/StarDrawer.js
            class StarDrawer {
                getSidesCount(particle) {
                    var _a, _b;

                    const star = particle.shapeData;
                    return (_b = (_a = star === null || star === void 0 ? void 0 : star.sides) !== null && _a !== void 0 ? _a : star === null || star === void 0 ? void 0 : star.nb_sides) !== null && _b !== void 0 ? _b : 5;
                }

                draw(context, particle, radius) {
                    var _a;

                    const star = particle.shapeData;
                    const sides = this.getSidesCount(particle);
                    const inset = (_a = star === null || star === void 0 ? void 0 : star.inset) !== null && _a !== void 0 ? _a : 2;
                    context.moveTo(0, 0 - radius);

                    for (let i = 0; i < sides; i++) {
                        context.rotate(Math.PI / sides);
                        context.lineTo(0, 0 - radius * inset);
                        context.rotate(Math.PI / sides);
                        context.lineTo(0, 0 - radius);
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Shapes/Star/index.js

            function loadStarShape(tsParticles) {
                tsParticles.addShape("star", new StarDrawer());
            }
            ;// CONCATENATED MODULE: ./dist/browser/Shapes/Text/TextDrawer.js
            var TextDrawer_awaiter = undefined && undefined.__awaiter || function (thisArg, _arguments, P, generator) {
                function adopt(value) {
                    return value instanceof P ? value : new P(function (resolve) {
                        resolve(value);
                    });
                }

                return new (P || (P = Promise))(function (resolve, reject) {
                    function fulfilled(value) {
                        try {
                            step(generator.next(value));
                        } catch (e) {
                            reject(e);
                        }
                    }

                    function rejected(value) {
                        try {
                            step(generator["throw"](value));
                        } catch (e) {
                            reject(e);
                        }
                    }

                    function step(result) {
                        result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected);
                    }

                    step((generator = generator.apply(thisArg, _arguments || [])).next());
                });
            };


            const validTypes = ["text", "character", "char"];
            class TextDrawer {
                getSidesCount() {
                    return 12;
                }

                init(container) {
                    return TextDrawer_awaiter(this, void 0, void 0, function* () {
                        const options = container.actualOptions;

                        if (validTypes.find(t => isInArray(t, options.particles.shape.type))) {
                            const shapeOptions = validTypes.map(t => options.particles.shape.options[t]).find(t => !!t);

                            if (shapeOptions instanceof Array) {
                                const promises = [];

                                for (const character of shapeOptions) {
                                    promises.push(loadFont(character));
                                }

                                yield Promise.allSettled(promises);
                            } else {
                                if (shapeOptions !== undefined) {
                                    yield loadFont(shapeOptions);
                                }
                            }
                        }
                    });
                }

                draw(context, particle, radius, opacity) {
                    var _a, _b, _c;

                    const character = particle.shapeData;

                    if (character === undefined) {
                        return;
                    }

                    const textData = character.value;

                    if (textData === undefined) {
                        return;
                    }

                    const textParticle = particle;

                    if (textParticle.text === undefined) {
                        textParticle.text = textData instanceof Array ? itemFromArray(textData, particle.randomIndexData) : textData;
                    }

                    const text = textParticle.text;
                    const style = (_a = character.style) !== null && _a !== void 0 ? _a : "";
                    const weight = (_b = character.weight) !== null && _b !== void 0 ? _b : "400";
                    const size = Math.round(radius) * 2;
                    const font = (_c = character.font) !== null && _c !== void 0 ? _c : "Verdana";
                    const fill = particle.fill;
                    const offsetX = text.length * radius / 2;
                    context.font = `${style} ${weight} ${size}px "${font}"`;
                    const pos = {
                        x: -offsetX,
                        y: radius / 2
                    };
                    context.globalAlpha = opacity;

                    if (fill) {
                        context.fillText(text, pos.x, pos.y);
                    } else {
                        context.strokeText(text, pos.x, pos.y);
                    }

                    context.globalAlpha = 1;
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Shapes/Text/index.js

            function loadTextShape(tsParticles) {
                const drawer = new TextDrawer();

                for (const type of validTypes) {
                    tsParticles.addShape(type, drawer);
                }
            }
            ;// CONCATENATED MODULE: ./dist/browser/Updaters/Angle/AngleUpdater.js


            function updateAngle(particle, delta) {
                var _a;

                const rotate = particle.options.rotate;
                const rotateAnimation = rotate.animation;
                const speed = ((_a = particle.rotate.velocity) !== null && _a !== void 0 ? _a : 0) * delta.factor;
                const max = 2 * Math.PI;

                if (!rotateAnimation.enable) {
                    return;
                }

                switch (particle.rotate.status) {
                    case AnimationStatus.increasing:
                        particle.rotate.value += speed;

                        if (particle.rotate.value > max) {
                            particle.rotate.value -= max;
                        }

                        break;

                    case AnimationStatus.decreasing:
                    default:
                        particle.rotate.value -= speed;

                        if (particle.rotate.value < 0) {
                            particle.rotate.value += max;
                        }

                        break;
                }
            }

            class AngleUpdater {
                isEnabled(particle) {
                    const rotate = particle.options.rotate;
                    const rotateAnimation = rotate.animation;
                    return !particle.destroyed && !particle.spawning && !rotate.path && rotateAnimation.enable;
                }

                update(particle, delta) {
                    if (!this.isEnabled(particle)) {
                        return;
                    }

                    updateAngle(particle, delta);
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Updaters/Angle/index.js

            function loadAngleUpdater(tsParticles) {
                tsParticles.addParticleUpdater("angle", () => new AngleUpdater());
            }
            ;// CONCATENATED MODULE: ./dist/browser/Updaters/Color/ColorUpdater.js



            function updateColorValue(delta, value, valueAnimation, max, decrease) {
                var _a;

                const colorValue = value;

                if (!colorValue || !valueAnimation.enable) {
                    return;
                }

                const offset = randomInRange(valueAnimation.offset);
                const velocity = ((_a = value.velocity) !== null && _a !== void 0 ? _a : 0) * delta.factor + offset * 3.6;

                if (!decrease || colorValue.status === AnimationStatus.increasing) {
                    colorValue.value += velocity;

                    if (decrease && colorValue.value > max) {
                        colorValue.status = AnimationStatus.decreasing;
                        colorValue.value -= colorValue.value % max;
                    }
                } else {
                    colorValue.value -= velocity;

                    if (colorValue.value < 0) {
                        colorValue.status = AnimationStatus.increasing;
                        colorValue.value += colorValue.value;
                    }
                }

                if (colorValue.value > max) {
                    colorValue.value %= max;
                }
            }

            function updateColor(particle, delta) {
                var _a, _b, _c;

                const animationOptions = particle.options.color.animation;

                if (((_a = particle.color) === null || _a === void 0 ? void 0 : _a.h) !== undefined) {
                    updateColorValue(delta, particle.color.h, animationOptions.h, 360, false);
                }

                if (((_b = particle.color) === null || _b === void 0 ? void 0 : _b.s) !== undefined) {
                    updateColorValue(delta, particle.color.s, animationOptions.s, 100, true);
                }

                if (((_c = particle.color) === null || _c === void 0 ? void 0 : _c.l) !== undefined) {
                    updateColorValue(delta, particle.color.l, animationOptions.l, 100, true);
                }
            }

            class ColorUpdater {
                isEnabled(particle) {
                    var _a, _b, _c;

                    const animationOptions = particle.options.color.animation;
                    return !particle.destroyed && !particle.spawning && (((_a = particle.color) === null || _a === void 0 ? void 0 : _a.h.value) !== undefined && animationOptions.h.enable || ((_b = particle.color) === null || _b === void 0 ? void 0 : _b.s.value) !== undefined && animationOptions.s.enable || ((_c = particle.color) === null || _c === void 0 ? void 0 : _c.l.value) !== undefined && animationOptions.l.enable);
                }

                update(particle, delta) {
                    updateColor(particle, delta);
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Updaters/Color/index.js

            function loadColorUpdater(tsParticles) {
                tsParticles.addParticleUpdater("color", () => new ColorUpdater());
            }
            ;// CONCATENATED MODULE: ./dist/browser/Updaters/Life/LifeUpdater.js

            class LifeUpdater {
                constructor(container) {
                    this.container = container;
                }

                isEnabled(particle) {
                    return !particle.destroyed;
                }

                update(particle, delta) {
                    if (!this.isEnabled(particle)) {
                        return;
                    }

                    const life = particle.life;
                    let justSpawned = false;

                    if (particle.spawning) {
                        life.delayTime += delta.value;

                        if (life.delayTime >= particle.life.delay) {
                            justSpawned = true;
                            particle.spawning = false;
                            life.delayTime = 0;
                            life.time = 0;
                        } else {
                            return;
                        }
                    }

                    if (life.duration === -1) {
                        return;
                    }

                    if (particle.spawning) {
                        return;
                    }

                    if (justSpawned) {
                        life.time = 0;
                    } else {
                        life.time += delta.value;
                    }

                    if (life.time < life.duration) {
                        return;
                    }

                    life.time = 0;

                    if (particle.life.count > 0) {
                        particle.life.count--;
                    }

                    if (particle.life.count === 0) {
                        particle.destroy();
                        return;
                    }

                    const canvasSize = this.container.canvas.size,
                        widthRange = setRangeValue(0, canvasSize.width),
                        heightRange = setRangeValue(0, canvasSize.width);
                    particle.position.x = randomInRange(widthRange);
                    particle.position.y = randomInRange(heightRange);
                    particle.spawning = true;
                    life.delayTime = 0;
                    life.time = 0;
                    particle.reset();
                    const lifeOptions = particle.options.life;
                    life.delay = getRangeValue(lifeOptions.delay.value) * 1000;
                    life.duration = getRangeValue(lifeOptions.duration.value) * 1000;
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Updaters/Life/index.js

            function loadLifeUpdater(tsParticles) {
                tsParticles.addParticleUpdater("life", container => new LifeUpdater(container));
            }
            ;// CONCATENATED MODULE: ./dist/browser/Updaters/Opacity/OpacityUpdater.js



            function checkDestroy(particle, destroy, value, minValue, maxValue) {
                switch (destroy) {
                    case DestroyType.max:
                        if (value >= maxValue) {
                            particle.destroy();
                        }

                        break;

                    case DestroyType.min:
                        if (value <= minValue) {
                            particle.destroy();
                        }

                        break;
                }
            }

            function updateOpacity(particle, delta) {
                var _a, _b;

                const opacityOpt = particle.options.opacity;
                const opacityAnim = opacityOpt.animation;
                const minValue = particle.opacity.min;
                const maxValue = particle.opacity.max;

                if (!(!particle.destroyed && opacityAnim.enable && (opacityAnim.count <= 0 || particle.loops.opacity < opacityAnim.count))) {
                    return;
                }

                switch (particle.opacity.status) {
                    case AnimationStatus.increasing:
                        if (particle.opacity.value >= maxValue) {
                            particle.opacity.status = AnimationStatus.decreasing;
                            particle.loops.opacity++;
                        } else {
                            particle.opacity.value += ((_a = particle.opacity.velocity) !== null && _a !== void 0 ? _a : 0) * delta.factor;
                        }

                        break;

                    case AnimationStatus.decreasing:
                        if (particle.opacity.value <= minValue) {
                            particle.opacity.status = AnimationStatus.increasing;
                            particle.loops.opacity++;
                        } else {
                            particle.opacity.value -= ((_b = particle.opacity.velocity) !== null && _b !== void 0 ? _b : 0) * delta.factor;
                        }

                        break;
                }

                checkDestroy(particle, opacityAnim.destroy, particle.opacity.value, minValue, maxValue);

                if (!particle.destroyed) {
                    particle.opacity.value = clamp(particle.opacity.value, minValue, maxValue);
                }
            }

            class OpacityUpdater {
                isEnabled(particle) {
                    const opacityAnim = particle.options.opacity.anim;
                    return !particle.destroyed && !particle.spawning && opacityAnim.enable && (opacityAnim.count <= 0 || particle.loops.opacity < opacityAnim.count);
                }

                update(particle, delta) {
                    if (!this.isEnabled(particle)) {
                        return;
                    }

                    updateOpacity(particle, delta);
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Updaters/Opacity/index.js

            function loadOpacityUpdater(tsParticles) {
                tsParticles.addParticleUpdater("opacity", () => new OpacityUpdater());
            }
            ;// CONCATENATED MODULE: ./dist/browser/Updaters/Size/SizeUpdater.js



            function SizeUpdater_checkDestroy(particle, destroy, value, minValue, maxValue) {
                switch (destroy) {
                    case DestroyType.max:
                        if (value >= maxValue) {
                            particle.destroy();
                        }

                        break;

                    case DestroyType.min:
                        if (value <= minValue) {
                            particle.destroy();
                        }

                        break;
                }
            }

            function updateSize(particle, delta) {
                var _a;

                const sizeOpt = particle.options.size;
                const sizeAnim = sizeOpt.animation;
                const sizeVelocity = ((_a = particle.size.velocity) !== null && _a !== void 0 ? _a : 0) * delta.factor;
                const minValue = particle.size.min;
                const maxValue = particle.size.max;

                if (!(!particle.destroyed && sizeAnim.enable && (sizeAnim.count <= 0 || particle.loops.size < sizeAnim.count))) {
                    return;
                }

                switch (particle.size.status) {
                    case AnimationStatus.increasing:
                        if (particle.size.value >= maxValue) {
                            particle.size.status = AnimationStatus.decreasing;
                            particle.loops.size++;
                        } else {
                            particle.size.value += sizeVelocity;
                        }

                        break;

                    case AnimationStatus.decreasing:
                        if (particle.size.value <= minValue) {
                            particle.size.status = AnimationStatus.increasing;
                            particle.loops.size++;
                        } else {
                            particle.size.value -= sizeVelocity;
                        }

                }

                SizeUpdater_checkDestroy(particle, sizeAnim.destroy, particle.size.value, minValue, maxValue);

                if (!particle.destroyed) {
                    particle.size.value = clamp(particle.size.value, minValue, maxValue);
                }
            }

            class SizeUpdater {
                isEnabled(particle) {
                    const sizeOpt = particle.options.size;
                    const sizeAnim = sizeOpt.animation;
                    return !particle.destroyed && !particle.spawning && sizeAnim.enable && (sizeAnim.count <= 0 || particle.loops.size < sizeAnim.count);
                }

                update(particle, delta) {
                    if (!this.isEnabled(particle)) {
                        return;
                    }

                    updateSize(particle, delta);
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Updaters/Size/index.js

            function loadSizeUpdater(tsParticles) {
                tsParticles.addParticleUpdater("size", () => new SizeUpdater());
            }
            ;// CONCATENATED MODULE: ./dist/browser/Updaters/StrokeColor/StrokeColorUpdater.js



            function StrokeColorUpdater_updateColorValue(delta, value, valueAnimation, max, decrease) {
                var _a;

                const colorValue = value;

                if (!colorValue || !valueAnimation.enable) {
                    return;
                }

                const offset = randomInRange(valueAnimation.offset);
                const velocity = ((_a = value.velocity) !== null && _a !== void 0 ? _a : 0) * delta.factor + offset * 3.6;

                if (!decrease || colorValue.status === AnimationStatus.increasing) {
                    colorValue.value += velocity;

                    if (decrease && colorValue.value > max) {
                        colorValue.status = AnimationStatus.decreasing;
                        colorValue.value -= colorValue.value % max;
                    }
                } else {
                    colorValue.value -= velocity;

                    if (colorValue.value < 0) {
                        colorValue.status = AnimationStatus.increasing;
                        colorValue.value += colorValue.value;
                    }
                }

                if (colorValue.value > max) {
                    colorValue.value %= max;
                }
            }

            function updateStrokeColor(particle, delta) {
                var _a, _b, _c, _d, _e, _f, _g, _h, _j, _k, _l, _m;

                if (!particle.stroke.color) {
                    return;
                }

                const animationOptions = particle.stroke.color.animation;
                const valueAnimations = animationOptions;

                if (valueAnimations.enable !== undefined) {
                    const hue = (_b = (_a = particle.strokeColor) === null || _a === void 0 ? void 0 : _a.h) !== null && _b !== void 0 ? _b : (_c = particle.color) === null || _c === void 0 ? void 0 : _c.h;

                    if (hue) {
                        StrokeColorUpdater_updateColorValue(delta, hue, valueAnimations, 360, false);
                    }
                } else {
                    const hslAnimations = animationOptions;
                    const h = (_e = (_d = particle.strokeColor) === null || _d === void 0 ? void 0 : _d.h) !== null && _e !== void 0 ? _e : (_f = particle.color) === null || _f === void 0 ? void 0 : _f.h;

                    if (h) {
                        StrokeColorUpdater_updateColorValue(delta, h, hslAnimations.h, 360, false);
                    }

                    const s = (_h = (_g = particle.strokeColor) === null || _g === void 0 ? void 0 : _g.s) !== null && _h !== void 0 ? _h : (_j = particle.color) === null || _j === void 0 ? void 0 : _j.s;

                    if (s) {
                        StrokeColorUpdater_updateColorValue(delta, s, hslAnimations.s, 100, true);
                    }

                    const l = (_l = (_k = particle.strokeColor) === null || _k === void 0 ? void 0 : _k.l) !== null && _l !== void 0 ? _l : (_m = particle.color) === null || _m === void 0 ? void 0 : _m.l;

                    if (l) {
                        StrokeColorUpdater_updateColorValue(delta, l, hslAnimations.l, 100, true);
                    }
                }
            }

            class StrokeColorUpdater {
                isEnabled(particle) {
                    var _a, _b, _c;

                    const color = particle.stroke.color;
                    return !particle.destroyed && !particle.spawning && color !== undefined && (((_a = particle.strokeColor) === null || _a === void 0 ? void 0 : _a.h.value) !== undefined && color.animation.h.enable || ((_b = particle.strokeColor) === null || _b === void 0 ? void 0 : _b.s.value) !== undefined && color.animation.s.enable || ((_c = particle.strokeColor) === null || _c === void 0 ? void 0 : _c.l.value) !== undefined && color.animation.l.enable);
                }

                update(particle, delta) {
                    if (!this.isEnabled(particle)) {
                        return;
                    }

                    updateStrokeColor(particle, delta);
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Updaters/StrokeColor/index.js

            function loadStrokeColorUpdater(tsParticles) {
                tsParticles.addParticleUpdater("strokeColor", () => new StrokeColorUpdater());
            }
            ;// CONCATENATED MODULE: ./dist/browser/Updaters/OutModes/Utils.js


            function bounceHorizontal(data) {
                if (!(data.outMode === OutMode.bounce || data.outMode === OutMode.bounceHorizontal || data.outMode === "bounceHorizontal" || data.outMode === OutMode.split)) {
                    return;
                }

                const velocity = data.particle.velocity.x;
                let bounced = false;

                if (data.direction === OutModeDirection.right && data.bounds.right >= data.canvasSize.width && velocity > 0 || data.direction === OutModeDirection.left && data.bounds.left <= 0 && velocity < 0) {
                    const newVelocity = getRangeValue(data.particle.options.bounce.horizontal.value);
                    data.particle.velocity.x *= -newVelocity;
                    bounced = true;
                }

                if (!bounced) {
                    return;
                }

                const minPos = data.offset.x + data.size;

                if (data.bounds.right >= data.canvasSize.width) {
                    data.particle.position.x = data.canvasSize.width - minPos;
                } else if (data.bounds.left <= 0) {
                    data.particle.position.x = minPos;
                }

                if (data.outMode === OutMode.split) {
                    data.particle.destroy();
                }
            }
            function bounceVertical(data) {
                if (data.outMode === OutMode.bounce || data.outMode === OutMode.bounceVertical || data.outMode === "bounceVertical" || data.outMode === OutMode.split) {
                    const velocity = data.particle.velocity.y;
                    let bounced = false;

                    if (data.direction === OutModeDirection.bottom && data.bounds.bottom >= data.canvasSize.height && velocity > 0 || data.direction === OutModeDirection.top && data.bounds.top <= 0 && velocity < 0) {
                        const newVelocity = getRangeValue(data.particle.options.bounce.vertical.value);
                        data.particle.velocity.y *= -newVelocity;
                        bounced = true;
                    }

                    if (!bounced) {
                        return;
                    }

                    const minPos = data.offset.y + data.size;

                    if (data.bounds.bottom >= data.canvasSize.height) {
                        data.particle.position.y = data.canvasSize.height - minPos;
                    } else if (data.bounds.top <= 0) {
                        data.particle.position.y = minPos;
                    }

                    if (data.outMode === OutMode.split) {
                        data.particle.destroy();
                    }
                }
            }
            ;// CONCATENATED MODULE: ./dist/browser/Updaters/OutModes/OutOfCanvasUpdater.js



            class OutOfCanvasUpdater {
                constructor(container) {
                    this.container = container;
                }

                isEnabled(particle) {
                    return !particle.destroyed && !particle.spawning;
                }

                update(particle, delta) {
                    var _a, _b, _c, _d;

                    const outModes = particle.options.move.outModes;
                    this.updateOutMode(particle, delta, (_a = outModes.bottom) !== null && _a !== void 0 ? _a : outModes.default, OutModeDirection.bottom);
                    this.updateOutMode(particle, delta, (_b = outModes.left) !== null && _b !== void 0 ? _b : outModes.default, OutModeDirection.left);
                    this.updateOutMode(particle, delta, (_c = outModes.right) !== null && _c !== void 0 ? _c : outModes.default, OutModeDirection.right);
                    this.updateOutMode(particle, delta, (_d = outModes.top) !== null && _d !== void 0 ? _d : outModes.default, OutModeDirection.top);
                }

                updateOutMode(particle, delta, outMode, direction) {
                    switch (outMode) {
                        case OutMode.bounce:
                        case OutMode.bounceVertical:
                        case OutMode.bounceHorizontal:
                        case "bounceVertical":
                        case "bounceHorizontal":
                        case OutMode.split:
                            this.bounce(particle, delta, direction, outMode);
                            break;

                        case OutMode.destroy:
                            this.destroy(particle, direction);
                            break;

                        case OutMode.out:
                            this.out(particle, direction);
                            break;

                        case OutMode.none:
                        default:
                            this.none(particle, direction);
                            break;
                    }
                }

                destroy(particle, direction) {
                    const container = this.container;

                    if (isPointInside(particle.position, container.canvas.size, particle.getRadius(), direction)) {
                        return;
                    }

                    container.particles.remove(particle, undefined, true);
                }

                out(particle, direction) {
                    const container = this.container;

                    if (isPointInside(particle.position, container.canvas.size, particle.getRadius(), direction)) {
                        return;
                    }

                    const wrap = particle.options.move.warp,
                        canvasSize = container.canvas.size,
                        newPos = {
                            bottom: canvasSize.height + particle.offset.y,
                            left: -particle.offset.x,
                            right: canvasSize.width + particle.offset.x,
                            top: -particle.offset.y
                        },
                        sizeValue = particle.getRadius(),
                        nextBounds = calculateBounds(particle.position, sizeValue);

                    if (direction === OutModeDirection.right && nextBounds.left > canvasSize.width + particle.offset.x) {
                        particle.position.x = newPos.left;
                        particle.initialPosition.x = particle.position.x;

                        if (!wrap) {
                            particle.position.y = Math.random() * canvasSize.height;
                            particle.initialPosition.y = particle.position.y;
                        }
                    } else if (direction === OutModeDirection.left && nextBounds.right < -particle.offset.x) {
                        particle.position.x = newPos.right;
                        particle.initialPosition.x = particle.position.x;

                        if (!wrap) {
                            particle.position.y = Math.random() * canvasSize.height;
                            particle.initialPosition.y = particle.position.y;
                        }
                    }

                    if (direction === OutModeDirection.bottom && nextBounds.top > canvasSize.height + particle.offset.y) {
                        if (!wrap) {
                            particle.position.x = Math.random() * canvasSize.width;
                            particle.initialPosition.x = particle.position.x;
                        }

                        particle.position.y = newPos.top;
                        particle.initialPosition.y = particle.position.y;
                    } else if (direction === OutModeDirection.top && nextBounds.bottom < -particle.offset.y) {
                        if (!wrap) {
                            particle.position.x = Math.random() * canvasSize.width;
                            particle.initialPosition.x = particle.position.x;
                        }

                        particle.position.y = newPos.bottom;
                        particle.initialPosition.y = particle.position.y;
                    }
                }

                bounce(particle, delta, direction, outMode) {
                    const container = this.container;
                    let handled = false;

                    for (const [, plugin] of container.plugins) {
                        if (plugin.particleBounce !== undefined) {
                            handled = plugin.particleBounce(particle, delta, direction);
                        }

                        if (handled) {
                            break;
                        }
                    }

                    if (handled) {
                        return;
                    }

                    const pos = particle.getPosition(),
                        offset = particle.offset,
                        size = particle.getRadius(),
                        bounds = calculateBounds(pos, size),
                        canvasSize = container.canvas.size;
                    bounceHorizontal({
                        particle,
                        outMode,
                        direction,
                        bounds,
                        canvasSize,
                        offset,
                        size
                    });
                    bounceVertical({
                        particle,
                        outMode,
                        direction,
                        bounds,
                        canvasSize,
                        offset,
                        size
                    });
                }

                none(particle, direction) {
                    if (particle.options.move.distance.horizontal && (direction === OutModeDirection.left || direction === OutModeDirection.right) || particle.options.move.distance.vertical && (direction === OutModeDirection.top || direction === OutModeDirection.bottom)) {
                        return;
                    }

                    const gravityOptions = particle.options.move.gravity,
                        container = this.container;

                    if (!gravityOptions.enable) {
                        if (!isPointInside(particle.position, container.canvas.size, particle.getRadius(), direction)) {
                            container.particles.remove(particle);
                        }
                    } else {
                        const position = particle.position;

                        if (!gravityOptions.inverse && position.y > container.canvas.size.height && direction === OutModeDirection.bottom || gravityOptions.inverse && position.y < 0 && direction === OutModeDirection.top) {
                            container.particles.remove(particle);
                        }
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Updaters/OutModes/index.js

            function loadOutModesUpdater(tsParticles) {
                tsParticles.addParticleUpdater("outModes", container => new OutOfCanvasUpdater(container));
            }
            ;// CONCATENATED MODULE: ./dist/browser/Updaters/Roll/RollUpdater.js
            function updateRoll(particle, delta) {
                const roll = particle.options.roll;
                const speed = particle.rollSpeed * delta.factor;
                const max = 2 * Math.PI;

                if (!roll.enable) {
                    return;
                }

                particle.rollAngle += speed;

                if (particle.rollAngle > max) {
                    particle.rollAngle -= max;
                }
            }

            class RollUpdater {
                isEnabled(particle) {
                    const rotate = particle.options.rotate;
                    const rotateAnimation = rotate.animation;
                    return !particle.destroyed && !particle.spawning && !rotate.path && rotateAnimation.enable;
                }

                update(particle, delta) {
                    if (!this.isEnabled(particle)) {
                        return;
                    }

                    updateRoll(particle, delta);
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Updaters/Roll/index.js

            function loadRollUpdater(tsParticles) {
                tsParticles.addParticleUpdater("roll", () => new RollUpdater());
            }
            ;// CONCATENATED MODULE: ./dist/browser/Updaters/Tilt/TiltUpdater.js


            function updateTilt(particle, delta) {
                var _a;

                const tilt = particle.options.tilt;
                const tiltAnimation = tilt.animation;
                const speed = ((_a = particle.tilt.velocity) !== null && _a !== void 0 ? _a : 0) * delta.factor;
                const max = 2 * Math.PI;

                if (!tiltAnimation.enable) {
                    return;
                }

                switch (particle.tilt.status) {
                    case AnimationStatus.increasing:
                        particle.tilt.value += speed;

                        if (particle.tilt.value > max) {
                            particle.tilt.value -= max;
                        }

                        break;

                    case AnimationStatus.decreasing:
                    default:
                        particle.tilt.value -= speed;

                        if (particle.tilt.value < 0) {
                            particle.tilt.value += max;
                        }

                        break;
                }
            }

            class TiltUpdater {
                isEnabled(particle) {
                    const rotate = particle.options.rotate;
                    const rotateAnimation = rotate.animation;
                    return !particle.destroyed && !particle.spawning && !rotate.path && rotateAnimation.enable;
                }

                update(particle, delta) {
                    if (!this.isEnabled(particle)) {
                        return;
                    }

                    updateTilt(particle, delta);
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Updaters/Tilt/index.js

            function loadTiltUpdater(tsParticles) {
                tsParticles.addParticleUpdater("tilt", () => new TiltUpdater());
            }
            ;// CONCATENATED MODULE: ./dist/browser/Updaters/Wobble/WobbleUpdater.js
            function updateWobble(particle, delta) {
                const wobble = particle.options.wobble;
                const speed = particle.wobbleSpeed * delta.factor;
                const distance = particle.wobbleDistance * delta.factor / (1000 / 60);
                const max = 2 * Math.PI;

                if (!wobble.enable) {
                    return;
                }

                particle.wobbleAngle += speed;

                if (particle.wobbleAngle > max) {
                    particle.wobbleAngle -= max;
                }

                particle.position.x += distance * Math.cos(particle.wobbleAngle);
                particle.position.y += distance * Math.abs(Math.sin(particle.wobbleAngle));
            }

            class WobbleUpdater {
                isEnabled(particle) {
                    const rotate = particle.options.rotate;
                    const rotateAnimation = rotate.animation;
                    return !particle.destroyed && !particle.spawning && !rotate.path && rotateAnimation.enable;
                }

                update(particle, delta) {
                    if (!this.isEnabled(particle)) {
                        return;
                    }

                    updateWobble(particle, delta);
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Updaters/Wobble/index.js

            function loadWobbleUpdater(tsParticles) {
                tsParticles.addParticleUpdater("wobble", () => new WobbleUpdater());
            }
            ;// CONCATENATED MODULE: ./dist/browser/Core/ExternalInteractorBase.js

            class ExternalInteractorBase {
                constructor(container) {
                    this.container = container;
                    this.type = InteractorType.External;
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Interactions/External/Attract/Attractor.js




            class Attractor extends ExternalInteractorBase {
                constructor(container) {
                    super(container);
                }

                isEnabled() {
                    const container = this.container,
                        options = container.actualOptions,
                        mouse = container.interactivity.mouse,
                        events = options.interactivity.events;

                    if ((!mouse.position || !events.onHover.enable) && (!mouse.clickPosition || !events.onClick.enable)) {
                        return false;
                    }

                    const hoverMode = events.onHover.mode,
                        clickMode = events.onClick.mode;
                    return isInArray(HoverMode.attract, hoverMode) || isInArray(ClickMode.attract, clickMode);
                }

                reset() { }

                interact() {
                    const container = this.container,
                        options = container.actualOptions,
                        mouseMoveStatus = container.interactivity.status === Constants.mouseMoveEvent,
                        events = options.interactivity.events,
                        hoverEnabled = events.onHover.enable,
                        hoverMode = events.onHover.mode,
                        clickEnabled = events.onClick.enable,
                        clickMode = events.onClick.mode;

                    if (mouseMoveStatus && hoverEnabled && isInArray(HoverMode.attract, hoverMode)) {
                        this.hoverAttract();
                    } else if (clickEnabled && isInArray(ClickMode.attract, clickMode)) {
                        this.clickAttract();
                    }
                }

                hoverAttract() {
                    const container = this.container;
                    const mousePos = container.interactivity.mouse.position;

                    if (!mousePos) {
                        return;
                    }

                    const attractRadius = container.retina.attractModeDistance;
                    this.processAttract(mousePos, attractRadius, new Circle(mousePos.x, mousePos.y, attractRadius));
                }

                processAttract(position, attractRadius, area) {
                    const container = this.container;
                    const attractOptions = container.actualOptions.interactivity.modes.attract;
                    const query = container.particles.quadTree.query(area);

                    for (const particle of query) {
                        const {
                            dx,
                            dy,
                            distance
                        } = getDistances(particle.position, position);
                        const velocity = attractOptions.speed * attractOptions.factor;
                        const attractFactor = clamp(calcEasing(1 - distance / attractRadius, attractOptions.easing) * velocity, 0, attractOptions.maxSpeed);
                        const normVec = Vector.create(distance === 0 ? velocity : dx / distance * attractFactor, distance === 0 ? velocity : dy / distance * attractFactor);
                        particle.position.subFrom(normVec);
                    }
                }

                clickAttract() {
                    const container = this.container;

                    if (!container.attract.finish) {
                        if (!container.attract.count) {
                            container.attract.count = 0;
                        }

                        container.attract.count++;

                        if (container.attract.count === container.particles.count) {
                            container.attract.finish = true;
                        }
                    }

                    if (container.attract.clicking) {
                        const mousePos = container.interactivity.mouse.clickPosition;

                        if (!mousePos) {
                            return;
                        }

                        const attractRadius = container.retina.attractModeDistance;
                        this.processAttract(mousePos, attractRadius, new Circle(mousePos.x, mousePos.y, attractRadius));
                    } else if (container.attract.clicking === false) {
                        container.attract.particles = [];
                    }

                    return;
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Interactions/External/Attract/index.js

            function loadExternalAttractInteraction(tsParticles) {
                tsParticles.addInteractor("externalAttract", container => new Attractor(container));
            }
            ;// CONCATENATED MODULE: ./dist/browser/Interactions/External/Bounce/Bouncer.js




            class Bouncer extends ExternalInteractorBase {
                constructor(container) {
                    super(container);
                }

                isEnabled() {
                    const container = this.container,
                        options = container.actualOptions,
                        mouse = container.interactivity.mouse,
                        events = options.interactivity.events,
                        divs = events.onDiv;
                    return mouse.position && events.onHover.enable && isInArray(HoverMode.bounce, events.onHover.mode) || isDivModeEnabled(DivMode.bounce, divs);
                }

                interact() {
                    const container = this.container,
                        options = container.actualOptions,
                        events = options.interactivity.events,
                        mouseMoveStatus = container.interactivity.status === Constants.mouseMoveEvent,
                        hoverEnabled = events.onHover.enable,
                        hoverMode = events.onHover.mode,
                        divs = events.onDiv;

                    if (mouseMoveStatus && hoverEnabled && isInArray(HoverMode.bounce, hoverMode)) {
                        this.processMouseBounce();
                    } else {
                        divModeExecute(DivMode.bounce, divs, (selector, div) => this.singleSelectorBounce(selector, div));
                    }
                }

                reset() { }

                processMouseBounce() {
                    const container = this.container,
                        pxRatio = container.retina.pixelRatio,
                        tolerance = 10 * pxRatio,
                        mousePos = container.interactivity.mouse.position,
                        radius = container.retina.bounceModeDistance;

                    if (mousePos) {
                        this.processBounce(mousePos, radius, new Circle(mousePos.x, mousePos.y, radius + tolerance));
                    }
                }

                singleSelectorBounce(selector, div) {
                    const container = this.container;
                    const query = document.querySelectorAll(selector);

                    if (!query.length) {
                        return;
                    }

                    query.forEach(item => {
                        const elem = item,
                            pxRatio = container.retina.pixelRatio,
                            pos = {
                                x: (elem.offsetLeft + elem.offsetWidth / 2) * pxRatio,
                                y: (elem.offsetTop + elem.offsetHeight / 2) * pxRatio
                            },
                            radius = elem.offsetWidth / 2 * pxRatio,
                            tolerance = 10 * pxRatio;
                        const area = div.type === DivType.circle ? new Circle(pos.x, pos.y, radius + tolerance) : new Rectangle(elem.offsetLeft * pxRatio - tolerance, elem.offsetTop * pxRatio - tolerance, elem.offsetWidth * pxRatio + tolerance * 2, elem.offsetHeight * pxRatio + tolerance * 2);
                        this.processBounce(pos, radius, area);
                    });
                }

                processBounce(position, radius, area) {
                    const query = this.container.particles.quadTree.query(area);

                    for (const particle of query) {
                        if (area instanceof Circle) {
                            circleBounce(circleBounceDataFromParticle(particle), {
                                position,
                                radius,
                                mass: Math.pow(radius, 2) * Math.PI / 2,
                                velocity: Vector.origin,
                                factor: Vector.origin
                            });
                        } else if (area instanceof Rectangle) {
                            rectBounce(particle, calculateBounds(position, radius));
                        }
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Interactions/External/Bounce/index.js

            function loadExternalBounceInteraction(tsParticles) {
                tsParticles.addInteractor("externalBounce", container => new Bouncer(container));
            }
            ;// CONCATENATED MODULE: ./dist/browser/Interactions/External/Bubble/ProcessBubbleType.js
            var ProcessBubbleType;

            (function (ProcessBubbleType) {
                ProcessBubbleType["color"] = "color";
                ProcessBubbleType["opacity"] = "opacity";
                ProcessBubbleType["size"] = "size";
            })(ProcessBubbleType || (ProcessBubbleType = {}));
            ;// CONCATENATED MODULE: ./dist/browser/Interactions/External/Bubble/Bubbler.js





            function calculateBubbleValue(particleValue, modeValue, optionsValue, ratio) {
                if (modeValue >= optionsValue) {
                    const value = particleValue + (modeValue - optionsValue) * ratio;
                    return clamp(value, particleValue, modeValue);
                } else if (modeValue < optionsValue) {
                    const value = particleValue - (optionsValue - modeValue) * ratio;
                    return clamp(value, modeValue, particleValue);
                }
            }

            class Bubbler extends ExternalInteractorBase {
                constructor(container) {
                    super(container);
                }

                isEnabled() {
                    const container = this.container,
                        options = container.actualOptions,
                        mouse = container.interactivity.mouse,
                        events = options.interactivity.events,
                        divs = events.onDiv,
                        divBubble = isDivModeEnabled(DivMode.bubble, divs);

                    if (!(divBubble || events.onHover.enable && mouse.position || events.onClick.enable && mouse.clickPosition)) {
                        return false;
                    }

                    const hoverMode = events.onHover.mode;
                    const clickMode = events.onClick.mode;
                    return isInArray(HoverMode.bubble, hoverMode) || isInArray(ClickMode.bubble, clickMode) || divBubble;
                }

                reset(particle, force) {
                    if (!(!particle.bubble.inRange || force)) {
                        return;
                    }

                    delete particle.bubble.div;
                    delete particle.bubble.opacity;
                    delete particle.bubble.radius;
                    delete particle.bubble.color;
                }

                interact() {
                    const options = this.container.actualOptions,
                        events = options.interactivity.events,
                        onHover = events.onHover,
                        onClick = events.onClick,
                        hoverEnabled = onHover.enable,
                        hoverMode = onHover.mode,
                        clickEnabled = onClick.enable,
                        clickMode = onClick.mode,
                        divs = events.onDiv;

                    if (hoverEnabled && isInArray(HoverMode.bubble, hoverMode)) {
                        this.hoverBubble();
                    } else if (clickEnabled && isInArray(ClickMode.bubble, clickMode)) {
                        this.clickBubble();
                    } else {
                        divModeExecute(DivMode.bubble, divs, (selector, div) => this.singleSelectorHover(selector, div));
                    }
                }

                singleSelectorHover(selector, div) {
                    const container = this.container,
                        selectors = document.querySelectorAll(selector);

                    if (!selectors.length) {
                        return;
                    }

                    selectors.forEach(item => {
                        const elem = item,
                            pxRatio = container.retina.pixelRatio,
                            pos = {
                                x: (elem.offsetLeft + elem.offsetWidth / 2) * pxRatio,
                                y: (elem.offsetTop + elem.offsetHeight / 2) * pxRatio
                            },
                            repulseRadius = elem.offsetWidth / 2 * pxRatio,
                            area = div.type === DivType.circle ? new Circle(pos.x, pos.y, repulseRadius) : new Rectangle(elem.offsetLeft * pxRatio, elem.offsetTop * pxRatio, elem.offsetWidth * pxRatio, elem.offsetHeight * pxRatio),
                            query = container.particles.quadTree.query(area);

                        for (const particle of query) {
                            if (!area.contains(particle.getPosition())) {
                                continue;
                            }

                            particle.bubble.inRange = true;
                            const divs = container.actualOptions.interactivity.modes.bubble.divs;
                            const divBubble = divMode(divs, elem);

                            if (!particle.bubble.div || particle.bubble.div !== elem) {
                                this.reset(particle, true);
                                particle.bubble.div = elem;
                            }

                            this.hoverBubbleSize(particle, 1, divBubble);
                            this.hoverBubbleOpacity(particle, 1, divBubble);
                            this.hoverBubbleColor(particle, 1, divBubble);
                        }
                    });
                }

                process(particle, distMouse, timeSpent, data) {
                    const container = this.container,
                        bubbleParam = data.bubbleObj.optValue;

                    if (bubbleParam === undefined) {
                        return;
                    }

                    const options = container.actualOptions,
                        bubbleDuration = options.interactivity.modes.bubble.duration,
                        bubbleDistance = container.retina.bubbleModeDistance,
                        particlesParam = data.particlesObj.optValue,
                        pObjBubble = data.bubbleObj.value,
                        pObj = data.particlesObj.value || 0,
                        type = data.type;

                    if (bubbleParam === particlesParam) {
                        return;
                    }

                    if (!container.bubble.durationEnd) {
                        if (distMouse <= bubbleDistance) {
                            const obj = pObjBubble !== null && pObjBubble !== void 0 ? pObjBubble : pObj;

                            if (obj !== bubbleParam) {
                                const value = pObj - timeSpent * (pObj - bubbleParam) / bubbleDuration;

                                if (type === ProcessBubbleType.size) {
                                    particle.bubble.radius = value;
                                }

                                if (type === ProcessBubbleType.opacity) {
                                    particle.bubble.opacity = value;
                                }
                            }
                        } else {
                            if (type === ProcessBubbleType.size) {
                                delete particle.bubble.radius;
                            }

                            if (type === ProcessBubbleType.opacity) {
                                delete particle.bubble.opacity;
                            }
                        }
                    } else if (pObjBubble) {
                        if (type === ProcessBubbleType.size) {
                            delete particle.bubble.radius;
                        }

                        if (type === ProcessBubbleType.opacity) {
                            delete particle.bubble.opacity;
                        }
                    }
                }

                clickBubble() {
                    const container = this.container,
                        options = container.actualOptions,
                        mouseClickPos = container.interactivity.mouse.clickPosition;

                    if (!mouseClickPos) {
                        return;
                    }

                    const distance = container.retina.bubbleModeDistance,
                        query = container.particles.quadTree.queryCircle(mouseClickPos, distance);

                    for (const particle of query) {
                        if (!container.bubble.clicking) {
                            continue;
                        }

                        particle.bubble.inRange = !container.bubble.durationEnd;
                        const pos = particle.getPosition(),
                            distMouse = getDistance(pos, mouseClickPos),
                            timeSpent = (new Date().getTime() - (container.interactivity.mouse.clickTime || 0)) / 1000;

                        if (timeSpent > options.interactivity.modes.bubble.duration) {
                            container.bubble.durationEnd = true;
                        }

                        if (timeSpent > options.interactivity.modes.bubble.duration * 2) {
                            container.bubble.clicking = false;
                            container.bubble.durationEnd = false;
                        }

                        const sizeData = {
                            bubbleObj: {
                                optValue: container.retina.bubbleModeSize,
                                value: particle.bubble.radius
                            },
                            particlesObj: {
                                optValue: getRangeMax(particle.options.size.value) * container.retina.pixelRatio,
                                value: particle.size.value
                            },
                            type: ProcessBubbleType.size
                        };
                        this.process(particle, distMouse, timeSpent, sizeData);
                        const opacityData = {
                            bubbleObj: {
                                optValue: options.interactivity.modes.bubble.opacity,
                                value: particle.bubble.opacity
                            },
                            particlesObj: {
                                optValue: getRangeMax(particle.options.opacity.value),
                                value: particle.opacity.value
                            },
                            type: ProcessBubbleType.opacity
                        };
                        this.process(particle, distMouse, timeSpent, opacityData);

                        if (!container.bubble.durationEnd) {
                            if (distMouse <= container.retina.bubbleModeDistance) {
                                this.hoverBubbleColor(particle, distMouse);
                            } else {
                                delete particle.bubble.color;
                            }
                        } else {
                            delete particle.bubble.color;
                        }
                    }
                }

                hoverBubble() {
                    const container = this.container,
                        mousePos = container.interactivity.mouse.position;

                    if (mousePos === undefined) {
                        return;
                    }

                    const distance = container.retina.bubbleModeDistance,
                        query = container.particles.quadTree.queryCircle(mousePos, distance);

                    for (const particle of query) {
                        particle.bubble.inRange = true;
                        const pos = particle.getPosition(),
                            pointDistance = getDistance(pos, mousePos),
                            ratio = 1 - pointDistance / distance;

                        if (pointDistance <= distance) {
                            if (ratio >= 0 && container.interactivity.status === Constants.mouseMoveEvent) {
                                this.hoverBubbleSize(particle, ratio);
                                this.hoverBubbleOpacity(particle, ratio);
                                this.hoverBubbleColor(particle, ratio);
                            }
                        } else {
                            this.reset(particle);
                        }

                        if (container.interactivity.status === Constants.mouseLeaveEvent) {
                            this.reset(particle);
                        }
                    }
                }

                hoverBubbleSize(particle, ratio, divBubble) {
                    const container = this.container,
                        modeSize = (divBubble === null || divBubble === void 0 ? void 0 : divBubble.size) ? divBubble.size * container.retina.pixelRatio : container.retina.bubbleModeSize;

                    if (modeSize === undefined) {
                        return;
                    }

                    const optSize = getRangeMax(particle.options.size.value) * container.retina.pixelRatio;
                    const pSize = particle.size.value;
                    const size = calculateBubbleValue(pSize, modeSize, optSize, ratio);

                    if (size !== undefined) {
                        particle.bubble.radius = size;
                    }
                }

                hoverBubbleOpacity(particle, ratio, divBubble) {
                    var _a;

                    const container = this.container,
                        options = container.actualOptions,
                        modeOpacity = (_a = divBubble === null || divBubble === void 0 ? void 0 : divBubble.opacity) !== null && _a !== void 0 ? _a : options.interactivity.modes.bubble.opacity;

                    if (!modeOpacity) {
                        return;
                    }

                    const optOpacity = particle.options.opacity.value;
                    const pOpacity = particle.opacity.value;
                    const opacity = calculateBubbleValue(pOpacity, modeOpacity, getRangeMax(optOpacity), ratio);

                    if (opacity !== undefined) {
                        particle.bubble.opacity = opacity;
                    }
                }

                hoverBubbleColor(particle, ratio, divBubble) {
                    const options = this.container.actualOptions;
                    const bubbleOptions = divBubble !== null && divBubble !== void 0 ? divBubble : options.interactivity.modes.bubble;

                    if (!particle.bubble.finalColor) {
                        const modeColor = bubbleOptions.color;

                        if (!modeColor) {
                            return;
                        }

                        const bubbleColor = modeColor instanceof Array ? itemFromArray(modeColor) : modeColor;
                        particle.bubble.finalColor = colorToHsl(bubbleColor);
                    }

                    if (!particle.bubble.finalColor) {
                        return;
                    }

                    if (bubbleOptions.mix) {
                        particle.bubble.color = undefined;
                        const pColor = particle.getFillColor();
                        particle.bubble.color = pColor ? rgbToHsl(colorMix(pColor, particle.bubble.finalColor, 1 - ratio, ratio)) : particle.bubble.finalColor;
                    } else {
                        particle.bubble.color = particle.bubble.finalColor;
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Interactions/External/Bubble/index.js

            function loadExternalBubbleInteraction(tsParticles) {
                tsParticles.addInteractor("externalBubble", container => new Bubbler(container));
            }
            ;// CONCATENATED MODULE: ./dist/browser/Interactions/External/Connect/Connector.js



            class Connector extends ExternalInteractorBase {
                constructor(container) {
                    super(container);
                }

                isEnabled() {
                    const container = this.container,
                        mouse = container.interactivity.mouse,
                        events = container.actualOptions.interactivity.events;

                    if (!(events.onHover.enable && mouse.position)) {
                        return false;
                    }

                    return isInArray(HoverMode.connect, events.onHover.mode);
                }

                reset() { }

                interact() {
                    const container = this.container,
                        options = container.actualOptions;

                    if (options.interactivity.events.onHover.enable && container.interactivity.status === "mousemove") {
                        const mousePos = container.interactivity.mouse.position;

                        if (!mousePos) {
                            return;
                        }

                        const distance = Math.abs(container.retina.connectModeRadius),
                            query = container.particles.quadTree.queryCircle(mousePos, distance);
                        let i = 0;

                        for (const p1 of query) {
                            const pos1 = p1.getPosition();

                            for (const p2 of query.slice(i + 1)) {
                                const pos2 = p2.getPosition(),
                                    distMax = Math.abs(container.retina.connectModeDistance),
                                    xDiff = Math.abs(pos1.x - pos2.x),
                                    yDiff = Math.abs(pos1.y - pos2.y);

                                if (xDiff < distMax && yDiff < distMax) {
                                    container.canvas.drawConnectLine(p1, p2);
                                }
                            }

                            ++i;
                        }
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Interactions/External/Connect/index.js

            function loadExternalConnectInteraction(tsParticles) {
                tsParticles.addInteractor("externalConnect", container => new Connector(container));
            }
            ;// CONCATENATED MODULE: ./dist/browser/Interactions/External/Grab/Grabber.js



            class Grabber extends ExternalInteractorBase {
                constructor(container) {
                    super(container);
                }

                isEnabled() {
                    const container = this.container,
                        mouse = container.interactivity.mouse,
                        events = container.actualOptions.interactivity.events;
                    return events.onHover.enable && !!mouse.position && isInArray(HoverMode.grab, events.onHover.mode);
                }

                reset() { }

                interact() {
                    var _a;

                    const container = this.container,
                        options = container.actualOptions,
                        interactivity = options.interactivity;

                    if (interactivity.events.onHover.enable && container.interactivity.status === Constants.mouseMoveEvent) {
                        const mousePos = container.interactivity.mouse.position;

                        if (!mousePos) {
                            return;
                        }

                        const distance = container.retina.grabModeDistance,
                            query = container.particles.quadTree.queryCircle(mousePos, distance);

                        for (const particle of query) {
                            const pos = particle.getPosition(),
                                pointDistance = getDistance(pos, mousePos);

                            if (pointDistance <= distance) {
                                const grabLineOptions = interactivity.modes.grab.links,
                                    lineOpacity = grabLineOptions.opacity,
                                    opacityLine = lineOpacity - pointDistance * lineOpacity / distance;

                                if (opacityLine <= 0) {
                                    continue;
                                }

                                const optColor = (_a = grabLineOptions.color) !== null && _a !== void 0 ? _a : particle.options.links.color;

                                if (!container.particles.grabLineColor) {
                                    const linksOptions = options.interactivity.modes.grab.links;
                                    container.particles.grabLineColor = getLinkRandomColor(optColor, linksOptions.blink, linksOptions.consent);
                                }

                                const colorLine = getLinkColor(particle, undefined, container.particles.grabLineColor);

                                if (!colorLine) {
                                    return;
                                }

                                container.canvas.drawGrabLine(particle, colorLine, opacityLine, mousePos);
                            }
                        }
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Interactions/External/Grab/index.js

            function loadExternalGrabInteraction(tsParticles) {
                tsParticles.addInteractor("externalGrab", container => new Grabber(container));
            }
            ;// CONCATENATED MODULE: ./dist/browser/Interactions/External/Repulse/Repulser.js




            class Repulser extends ExternalInteractorBase {
                constructor(container) {
                    super(container);
                }

                isEnabled() {
                    const container = this.container,
                        options = container.actualOptions,
                        mouse = container.interactivity.mouse,
                        events = options.interactivity.events,
                        divs = events.onDiv,
                        divRepulse = isDivModeEnabled(DivMode.repulse, divs);

                    if (!(divRepulse || events.onHover.enable && mouse.position || events.onClick.enable && mouse.clickPosition)) {
                        return false;
                    }

                    const hoverMode = events.onHover.mode,
                        clickMode = events.onClick.mode;
                    return isInArray(HoverMode.repulse, hoverMode) || isInArray(ClickMode.repulse, clickMode) || divRepulse;
                }

                reset() { }

                interact() {
                    const container = this.container,
                        options = container.actualOptions,
                        mouseMoveStatus = container.interactivity.status === Constants.mouseMoveEvent,
                        events = options.interactivity.events,
                        hoverEnabled = events.onHover.enable,
                        hoverMode = events.onHover.mode,
                        clickEnabled = events.onClick.enable,
                        clickMode = events.onClick.mode,
                        divs = events.onDiv;

                    if (mouseMoveStatus && hoverEnabled && isInArray(HoverMode.repulse, hoverMode)) {
                        this.hoverRepulse();
                    } else if (clickEnabled && isInArray(ClickMode.repulse, clickMode)) {
                        this.clickRepulse();
                    } else {
                        divModeExecute(DivMode.repulse, divs, (selector, div) => this.singleSelectorRepulse(selector, div));
                    }
                }

                singleSelectorRepulse(selector, div) {
                    const container = this.container,
                        query = document.querySelectorAll(selector);

                    if (!query.length) {
                        return;
                    }

                    query.forEach(item => {
                        const elem = item,
                            pxRatio = container.retina.pixelRatio,
                            pos = {
                                x: (elem.offsetLeft + elem.offsetWidth / 2) * pxRatio,
                                y: (elem.offsetTop + elem.offsetHeight / 2) * pxRatio
                            },
                            repulseRadius = elem.offsetWidth / 2 * pxRatio,
                            area = div.type === DivType.circle ? new Circle(pos.x, pos.y, repulseRadius) : new Rectangle(elem.offsetLeft * pxRatio, elem.offsetTop * pxRatio, elem.offsetWidth * pxRatio, elem.offsetHeight * pxRatio),
                            divs = container.actualOptions.interactivity.modes.repulse.divs,
                            divRepulse = divMode(divs, elem);
                        this.processRepulse(pos, repulseRadius, area, divRepulse);
                    });
                }

                hoverRepulse() {
                    const container = this.container,
                        mousePos = container.interactivity.mouse.position;

                    if (!mousePos) {
                        return;
                    }

                    const repulseRadius = container.retina.repulseModeDistance;
                    this.processRepulse(mousePos, repulseRadius, new Circle(mousePos.x, mousePos.y, repulseRadius));
                }

                processRepulse(position, repulseRadius, area, divRepulse) {
                    var _a;

                    const container = this.container,
                        query = container.particles.quadTree.query(area),
                        repulseOptions = container.actualOptions.interactivity.modes.repulse;

                    for (const particle of query) {
                        const {
                            dx,
                            dy,
                            distance
                        } = getDistances(particle.position, position),
                            velocity = ((_a = divRepulse === null || divRepulse === void 0 ? void 0 : divRepulse.speed) !== null && _a !== void 0 ? _a : repulseOptions.speed) * repulseOptions.factor,
                            repulseFactor = clamp(calcEasing(1 - distance / repulseRadius, repulseOptions.easing) * velocity, 0, repulseOptions.maxSpeed),
                            normVec = Vector.create(distance === 0 ? velocity : dx / distance * repulseFactor, distance === 0 ? velocity : dy / distance * repulseFactor);
                        particle.position.addTo(normVec);
                    }
                }

                clickRepulse() {
                    const container = this.container;

                    if (!container.repulse.finish) {
                        if (!container.repulse.count) {
                            container.repulse.count = 0;
                        }

                        container.repulse.count++;

                        if (container.repulse.count === container.particles.count) {
                            container.repulse.finish = true;
                        }
                    }

                    if (container.repulse.clicking) {
                        const repulseDistance = container.retina.repulseModeDistance,
                            repulseRadius = Math.pow(repulseDistance / 6, 3),
                            mouseClickPos = container.interactivity.mouse.clickPosition;

                        if (mouseClickPos === undefined) {
                            return;
                        }

                        const range = new Circle(mouseClickPos.x, mouseClickPos.y, repulseRadius),
                            query = container.particles.quadTree.query(range);

                        for (const particle of query) {
                            const {
                                dx,
                                dy,
                                distance
                            } = getDistances(mouseClickPos, particle.position),
                                d = Math.pow(distance, 2),
                                velocity = container.actualOptions.interactivity.modes.repulse.speed,
                                force = -repulseRadius * velocity / d;

                            if (d <= repulseRadius) {
                                container.repulse.particles.push(particle);
                                const vect = Vector.create(dx, dy);
                                vect.length = force;
                                particle.velocity.setTo(vect);
                            }
                        }
                    } else if (container.repulse.clicking === false) {
                        for (const particle of container.repulse.particles) {
                            particle.velocity.setTo(particle.initialVelocity);
                        }

                        container.repulse.particles = [];
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Interactions/External/Repulse/index.js

            function loadExternalRepulseInteraction(tsParticles) {
                tsParticles.addInteractor("externalRepulse", container => new Repulser(container));
            }
            ;// CONCATENATED MODULE: ./dist/browser/Interactions/External/Trail/TrailMaker.js



            class TrailMaker extends ExternalInteractorBase {
                constructor(container) {
                    super(container);
                    this.delay = 0;
                }

                interact(delta) {
                    var _a, _b, _c, _d;

                    if (!this.container.retina.reduceFactor) {
                        return;
                    }

                    const container = this.container,
                        options = container.actualOptions,
                        trailOptions = options.interactivity.modes.trail,
                        optDelay = trailOptions.delay * 1000 / this.container.retina.reduceFactor;

                    if (this.delay < optDelay) {
                        this.delay += delta.value;
                    }

                    if (this.delay < optDelay) {
                        return;
                    }

                    let canEmit = true;

                    if (trailOptions.pauseOnStop) {
                        if (container.interactivity.mouse.position === this.lastPosition || ((_a = container.interactivity.mouse.position) === null || _a === void 0 ? void 0 : _a.x) === ((_b = this.lastPosition) === null || _b === void 0 ? void 0 : _b.x) && ((_c = container.interactivity.mouse.position) === null || _c === void 0 ? void 0 : _c.y) === ((_d = this.lastPosition) === null || _d === void 0 ? void 0 : _d.y)) {
                            canEmit = false;
                        }
                    }

                    if (container.interactivity.mouse.position) {
                        this.lastPosition = {
                            x: container.interactivity.mouse.position.x,
                            y: container.interactivity.mouse.position.y
                        };
                    } else {
                        delete this.lastPosition;
                    }

                    if (canEmit) {
                        container.particles.push(trailOptions.quantity, container.interactivity.mouse, trailOptions.particles);
                    }

                    this.delay -= optDelay;
                }

                isEnabled() {
                    const container = this.container,
                        options = container.actualOptions,
                        mouse = container.interactivity.mouse,
                        events = options.interactivity.events;
                    return mouse.clicking && mouse.inside && !!mouse.position && isInArray(ClickMode.trail, events.onClick.mode) || mouse.inside && !!mouse.position && isInArray(HoverMode.trail, events.onHover.mode);
                }

                reset() { }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Interactions/External/Trail/index.js

            function loadExternalTrailInteraction(tsParticles) {
                tsParticles.addInteractor("externalTrail", container => new TrailMaker(container));
            }
            ;// CONCATENATED MODULE: ./dist/browser/Core/ParticlesInteractorBase.js

            class ParticlesInteractorBase {
                constructor(container) {
                    this.container = container;
                    this.type = InteractorType.Particles;
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Interactions/Particles/Attract/Attractor.js


            class Attractor_Attractor extends ParticlesInteractorBase {
                constructor(container) {
                    super(container);
                }

                interact(p1) {
                    var _a;

                    const container = this.container,
                        distance = (_a = p1.attractDistance) !== null && _a !== void 0 ? _a : container.retina.attractDistance,
                        pos1 = p1.getPosition(),
                        query = container.particles.quadTree.queryCircle(pos1, distance);

                    for (const p2 of query) {
                        if (p1 === p2 || !p2.options.move.attract.enable || p2.destroyed || p2.spawning) {
                            continue;
                        }

                        const pos2 = p2.getPosition(),
                            {
                                dx,
                                dy
                            } = getDistances(pos1, pos2),
                            rotate = p1.options.move.attract.rotate,
                            ax = dx / (rotate.x * 1000),
                            ay = dy / (rotate.y * 1000),
                            p1Factor = p2.size.value / p1.size.value,
                            p2Factor = 1 / p1Factor;
                        p1.velocity.x -= ax * p1Factor;
                        p1.velocity.y -= ay * p1Factor;
                        p2.velocity.x += ax * p2Factor;
                        p2.velocity.y += ay * p2Factor;
                    }
                }

                isEnabled(particle) {
                    return particle.options.move.attract.enable;
                }

                reset() { }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Interactions/Particles/Attract/index.js

            function loadParticlesAttractInteraction(tsParticles) {
                tsParticles.addInteractor("particlesAttract", container => new Attractor_Attractor(container));
            }
            ;// CONCATENATED MODULE: ./dist/browser/Interactions/Particles/Collisions/Collider.js




            function bounce(p1, p2) {
                circleBounce(circleBounceDataFromParticle(p1), circleBounceDataFromParticle(p2));
            }

            function destroy(p1, p2) {
                if (!p1.unbreakable && !p2.unbreakable) {
                    bounce(p1, p2);
                }

                if (p1.getRadius() === undefined && p2.getRadius() !== undefined) {
                    p1.destroy();
                } else if (p1.getRadius() !== undefined && p2.getRadius() === undefined) {
                    p2.destroy();
                } else if (p1.getRadius() !== undefined && p2.getRadius() !== undefined) {
                    if (p1.getRadius() >= p2.getRadius()) {
                        p2.destroy();
                    } else {
                        p1.destroy();
                    }
                }
            }

            class Collider extends ParticlesInteractorBase {
                constructor(container) {
                    super(container);
                }

                isEnabled(particle) {
                    return particle.options.collisions.enable;
                }

                reset() { }

                interact(p1) {
                    const container = this.container;
                    const pos1 = p1.getPosition();
                    const radius1 = p1.getRadius();
                    const query = container.particles.quadTree.queryCircle(pos1, radius1 * 2);

                    for (const p2 of query) {
                        if (p1 === p2 || !p2.options.collisions.enable || p1.options.collisions.mode !== p2.options.collisions.mode || p2.destroyed || p2.spawning) {
                            continue;
                        }

                        const pos2 = p2.getPosition();

                        if (Math.round(pos1.z) !== Math.round(pos2.z)) {
                            continue;
                        }

                        const dist = getDistance(pos1, pos2);
                        const radius2 = p2.getRadius();
                        const distP = radius1 + radius2;

                        if (dist <= distP) {
                            this.resolveCollision(p1, p2);
                        }
                    }
                }

                resolveCollision(p1, p2) {
                    switch (p1.options.collisions.mode) {
                        case CollisionMode.absorb:
                            {
                                this.absorb(p1, p2);
                                break;
                            }

                        case CollisionMode.bounce:
                            {
                                bounce(p1, p2);
                                break;
                            }

                        case CollisionMode.destroy:
                            {
                                destroy(p1, p2);
                                break;
                            }
                    }
                }

                absorb(p1, p2) {
                    const container = this.container;
                    const fps = container.fpsLimit / 1000;

                    if (p1.getRadius() === undefined && p2.getRadius() !== undefined) {
                        p1.destroy();
                    } else if (p1.getRadius() !== undefined && p2.getRadius() === undefined) {
                        p2.destroy();
                    } else if (p1.getRadius() !== undefined && p2.getRadius() !== undefined) {
                        if (p1.getRadius() >= p2.getRadius()) {
                            const factor = clamp(p1.getRadius() / p2.getRadius(), 0, p2.getRadius()) * fps;
                            p1.size.value += factor;
                            p2.size.value -= factor;

                            if (p2.getRadius() <= container.retina.pixelRatio) {
                                p2.size.value = 0;
                                p2.destroy();
                            }
                        } else {
                            const factor = clamp(p2.getRadius() / p1.getRadius(), 0, p1.getRadius()) * fps;
                            p1.size.value -= factor;
                            p2.size.value += factor;

                            if (p1.getRadius() <= container.retina.pixelRatio) {
                                p1.size.value = 0;
                                p1.destroy();
                            }
                        }
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Interactions/Particles/Collisions/index.js

            function loadParticlesCollisionsInteraction(tsParticles) {
                tsParticles.addInteractor("particlesCollisions", container => new Collider(container));
            }
            ;// CONCATENATED MODULE: ./dist/browser/Interactions/Particles/Links/Linker.js



            function getLinkDistance(pos1, pos2, optDistance, canvasSize, warp) {
                let distance = getDistance(pos1, pos2);

                if (!warp || distance <= optDistance) {
                    return distance;
                }

                const pos2NE = {
                    x: pos2.x - canvasSize.width,
                    y: pos2.y
                };
                distance = getDistance(pos1, pos2NE);

                if (distance <= optDistance) {
                    return distance;
                }

                const pos2SE = {
                    x: pos2.x - canvasSize.width,
                    y: pos2.y - canvasSize.height
                };
                distance = getDistance(pos1, pos2SE);

                if (distance <= optDistance) {
                    return distance;
                }

                const pos2SW = {
                    x: pos2.x,
                    y: pos2.y - canvasSize.height
                };
                distance = getDistance(pos1, pos2SW);
                return distance;
            }

            class Linker extends ParticlesInteractorBase {
                constructor(container) {
                    super(container);
                }

                isEnabled(particle) {
                    return particle.options.links.enable;
                }

                reset() { }

                interact(p1) {
                    var _a;

                    p1.links = [];
                    const pos1 = p1.getPosition();
                    const container = this.container;
                    const canvasSize = container.canvas.size;

                    if (pos1.x < 0 || pos1.y < 0 || pos1.x > canvasSize.width || pos1.y > canvasSize.height) {
                        return;
                    }

                    const linkOpt1 = p1.options.links;
                    const optOpacity = linkOpt1.opacity;
                    const optDistance = (_a = p1.linksDistance) !== null && _a !== void 0 ? _a : container.retina.linksDistance;
                    const warp = linkOpt1.warp;
                    const range = warp ? new CircleWarp(pos1.x, pos1.y, optDistance, canvasSize) : new Circle(pos1.x, pos1.y, optDistance);
                    const query = container.particles.quadTree.query(range);

                    for (const p2 of query) {
                        const linkOpt2 = p2.options.links;

                        if (p1 === p2 || !linkOpt2.enable || linkOpt1.id !== linkOpt2.id || p2.spawning || p2.destroyed || p1.links.map(t => t.destination).indexOf(p2) !== -1 || p2.links.map(t => t.destination).indexOf(p1) !== -1) {
                            continue;
                        }

                        const pos2 = p2.getPosition();

                        if (pos2.x < 0 || pos2.y < 0 || pos2.x > canvasSize.width || pos2.y > canvasSize.height) {
                            continue;
                        }

                        const distance = getLinkDistance(pos1, pos2, optDistance, canvasSize, warp && linkOpt2.warp);

                        if (distance > optDistance) {
                            return;
                        }

                        const opacityLine = (1 - distance / optDistance) * optOpacity;
                        this.setColor(p1);
                        p1.links.push({
                            destination: p2,
                            opacity: opacityLine
                        });
                    }
                }

                setColor(p1) {
                    const container = this.container;
                    const linksOptions = p1.options.links;
                    let linkColor = linksOptions.id === undefined ? container.particles.linksColor : container.particles.linksColors.get(linksOptions.id);

                    if (!linkColor) {
                        const optColor = linksOptions.color;
                        linkColor = getLinkRandomColor(optColor, linksOptions.blink, linksOptions.consent);

                        if (linksOptions.id === undefined) {
                            container.particles.linksColor = linkColor;
                        } else {
                            container.particles.linksColors.set(linksOptions.id, linkColor);
                        }
                    }
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Interactions/Particles/Links/LinkInstance.js

            class LinkInstance {
                constructor(container) {
                    this.container = container;
                }

                particleCreated(particle) {
                    const linkParticle = particle;
                    linkParticle.links = [];
                }

                particleDestroyed(particle) {
                    const linkParticle = particle;
                    linkParticle.links = [];
                }

                drawParticle(context, particle) {
                    const linkParticle = particle;
                    const container = this.container;
                    const particles = container.particles;
                    const pOptions = particle.options;

                    if (linkParticle.links.length > 0) {
                        context.save();
                        const p1Links = linkParticle.links.filter(l => {
                            const linkFreq = container.particles.getLinkFrequency(linkParticle, l.destination);
                            return linkFreq <= pOptions.links.frequency;
                        });

                        for (const link of p1Links) {
                            const p2 = link.destination;

                            if (pOptions.links.triangles.enable) {
                                const links = p1Links.map(l => l.destination);
                                const vertices = p2.links.filter(t => {
                                    const linkFreq = container.particles.getLinkFrequency(p2, t.destination);
                                    return linkFreq <= p2.options.links.frequency && links.indexOf(t.destination) >= 0;
                                });

                                if (vertices.length) {
                                    for (const vertex of vertices) {
                                        const p3 = vertex.destination;
                                        const triangleFreq = particles.getTriangleFrequency(linkParticle, p2, p3);

                                        if (triangleFreq > pOptions.links.triangles.frequency) {
                                            continue;
                                        }

                                        this.drawLinkTriangle(linkParticle, link, vertex);
                                    }
                                }
                            }

                            if (link.opacity > 0 && container.retina.linksWidth > 0) {
                                this.drawLinkLine(linkParticle, link);
                            }
                        }

                        context.restore();
                    }
                }

                drawLinkTriangle(p1, link1, link2) {
                    var _a;

                    const container = this.container;
                    const options = container.actualOptions;
                    const p2 = link1.destination;
                    const p3 = link2.destination;
                    const triangleOptions = p1.options.links.triangles;
                    const opacityTriangle = (_a = triangleOptions.opacity) !== null && _a !== void 0 ? _a : (link1.opacity + link2.opacity) / 2;

                    if (opacityTriangle <= 0) {
                        return;
                    }

                    const pos1 = p1.getPosition();
                    const pos2 = p2.getPosition();
                    const pos3 = p3.getPosition();
                    container.canvas.draw(ctx => {
                        if (getDistance(pos1, pos2) > container.retina.linksDistance || getDistance(pos3, pos2) > container.retina.linksDistance || getDistance(pos3, pos1) > container.retina.linksDistance) {
                            return;
                        }

                        let colorTriangle = colorToRgb(triangleOptions.color);

                        if (!colorTriangle) {
                            const linksOptions = p1.options.links;
                            const linkColor = linksOptions.id !== undefined ? container.particles.linksColors.get(linksOptions.id) : container.particles.linksColor;
                            colorTriangle = getLinkColor(p1, p2, linkColor);
                        }

                        if (!colorTriangle) {
                            return;
                        }

                        drawLinkTriangle(ctx, pos1, pos2, pos3, options.backgroundMask.enable, options.backgroundMask.composite, colorTriangle, opacityTriangle);
                    });
                }

                drawLinkLine(p1, link) {
                    const container = this.container;
                    const options = container.actualOptions;
                    const p2 = link.destination;
                    let opacity = link.opacity;
                    const pos1 = p1.getPosition();
                    const pos2 = p2.getPosition();
                    container.canvas.draw(ctx => {
                        var _a, _b;

                        let colorLine;
                        const twinkle = p1.options.twinkle.lines;

                        if (twinkle.enable) {
                            const twinkleFreq = twinkle.frequency;
                            const twinkleRgb = colorToRgb(twinkle.color);
                            const twinkling = Math.random() < twinkleFreq;

                            if (twinkling && twinkleRgb !== undefined) {
                                colorLine = twinkleRgb;
                                opacity = twinkle.opacity;
                            }
                        }

                        if (!colorLine) {
                            const linksOptions = p1.options.links;
                            const linkColor = linksOptions.id !== undefined ? container.particles.linksColors.get(linksOptions.id) : container.particles.linksColor;
                            colorLine = getLinkColor(p1, p2, linkColor);
                        }

                        if (!colorLine) {
                            return;
                        }

                        const width = (_a = p1.linksWidth) !== null && _a !== void 0 ? _a : container.retina.linksWidth;
                        const maxDistance = (_b = p1.linksDistance) !== null && _b !== void 0 ? _b : container.retina.linksDistance;
                        drawLinkLine(ctx, width, pos1, pos2, maxDistance, container.canvas.size, p1.options.links.warp, options.backgroundMask.enable, options.backgroundMask.composite, colorLine, opacity, p1.options.links.shadow);
                    });
                }

            }
            ;// CONCATENATED MODULE: ./dist/browser/Interactions/Particles/Links/plugin.js


            class Links_plugin_Plugin {
                constructor() {
                    this.id = "links";
                }

                getPlugin(container) {
                    return new LinkInstance(container);
                }

                needsPlugin() {
                    return true;
                }

                loadOptions() { }

            }

            function loadPlugin(tsParticles) {
                const plugin = new Links_plugin_Plugin();
                tsParticles.addPlugin(plugin);
            }
            ;// CONCATENATED MODULE: ./dist/browser/Interactions/Particles/Links/index.js


            function loadInteraction(tsParticles) {
                tsParticles.addInteractor("particlesLinks", container => new Linker(container));
            }
            function loadParticlesLinksInteraction(tsParticles) {
                loadInteraction(tsParticles);
                loadPlugin(tsParticles);
            }
            ;// CONCATENATED MODULE: ./dist/browser/slim.js




            function loadSlim(tsParticles) {
                loadExternalAttractInteraction(tsParticles);
                loadExternalBounceInteraction(tsParticles);
                loadExternalBubbleInteraction(tsParticles);
                loadExternalConnectInteraction(tsParticles);
                loadExternalGrabInteraction(tsParticles);
                loadExternalRepulseInteraction(tsParticles);
                loadExternalTrailInteraction(tsParticles);
                loadParticlesAttractInteraction(tsParticles);
                loadParticlesCollisionsInteraction(tsParticles);
                loadParticlesLinksInteraction(tsParticles);
                loadCircleShape(tsParticles);
                loadImageShape(tsParticles);
                loadLineShape(tsParticles);
                loadPolygonShape(tsParticles);
                loadSquareShape(tsParticles);
                loadStarShape(tsParticles);
                loadTextShape(tsParticles);
                loadLifeUpdater(tsParticles);
                loadOpacityUpdater(tsParticles);
                loadSizeUpdater(tsParticles);
                loadAngleUpdater(tsParticles);
                loadTiltUpdater(tsParticles);
                loadRollUpdater(tsParticles);
                loadWobbleUpdater(tsParticles);
                loadColorUpdater(tsParticles);
                loadStrokeColorUpdater(tsParticles);
                loadOutModesUpdater(tsParticles);
            }
            ;// CONCATENATED MODULE: ./dist/browser/full.js




            function loadFull(tsParticles) {
                loadSlim(tsParticles);
                loadAbsorbersPlugin(tsParticles);
                loadEmittersPlugin(tsParticles);
                loadPolygonMaskPlugin(tsParticles);
            }
            ;// CONCATENATED MODULE: ./dist/browser/index.js








            const tsParticles = new Main();
            tsParticles.init();
            loadFull(tsParticles);
            const {
                particlesJS,
                pJSDom
            } = initPjs(tsParticles);













/******/ 	return __webpack_exports__;
            /******/
        })()
            ;
    });

    (function ($) {

        $(window).on('elementor/frontend/init', function () {

            // we add paParticlesHandler on content load but only initialize it on elementor/init.
            if (undefined != window.paParticlesHandler) {

                var particalesObj = new paParticlesHandler('page', $('.elementor-page'), {});
                particalesObj.init();
            }

            var PremiumParticlesHandler = function ($scope, $) {

                if (!$scope.hasClass("premium-particles-yes"))
                    return;

                if ($('.particles-instant').length > 0) {
                    premiumParticlesHandler($scope);
                    return;
                }

                elementorFrontend.waypoint(
                    $scope,
                    function () {
                        premiumParticlesHandler($scope);
                    }
                );

            };

            elementorFrontend.hooks.addAction("frontend/element_ready/global", PremiumParticlesHandler);

        });

        window.paParticlesHandler = function (eleType, $scope, settings) {

            var self = this,
                pageSettings = {};

            this.isAddonEnabled = false;

            self.init = function () {

                this.isEditMode = elementorFrontend.isEditMode();

                // in case the addons is disabled on page load >> we listen to its switcher to re-init the addon when it's enabled.
                if (this.isEditMode) {
                    this.initAddonSwListener();
                    this.initCustomCss();
                    this.initZIndex();
                    this.initApplyOn();
                }

                self.initParticles();
            };

            self.initZIndex = function () {

                elementor.settings.page.addChangeCallback('premium_particles_zindex', self.onAddonZIndexChange);
            };

            self.initApplyOn = function () {

                elementor.settings.page.addChangeCallback('premium_particles_responsive', self.onAddonApplyOnChange);
            };

            self.initCustomCss = function () {

                elementor.settings.page.addChangeCallback('premium_particles_custom_style', self.onAddonCustomCssChange);
            };

            self.initAddonSwListener = function () {

                elementor.settings.page.addChangeCallback('premium_particles_switcher', self.onAddonSwitcherChange);
            };

            /** Addon's switcher call back function, re-init the particle on enabling, Destroy it on disabling. */
            self.onAddonSwitcherChange = function (newVal) {

                if ('yes' === newVal) {
                    self.initParticles();

                } else {
                    self.destroy();
                }
            };

            self.onAddonApplyOnChange = function (newVal) {
                currentDevice = elementorFrontend.getCurrentDeviceMode();

                if (-1 < newVal.indexOf(currentDevice)) {
                    self.initParticles();

                } else {
                    self.destroy();
                }
            };

            self.onAddonCustomCssChange = function (newVal) {

                if ('' !== newVal) {
                    self.initParticles();

                } else {
                    self.destroy();
                }
            };

            self.onAddonZIndexChange = function (newVal) {

                if ('' !== newVal) {
                    self.initParticles();

                } else {
                    self.destroy();
                }
            };

            self.destroy = function () {

                $scope.removeClass('premium-particles-yes premium-particles');

                $('.premium-global-particles-' + self.getPageId()).remove();
            };

            self.initParticles = function () {
                var settings = elementorFrontend.isEditMode() ? elementor.settings.page.model.attributes : elementorFrontend.config.settings.page;

                pageSettings = Object.assign({}, settings);

                if (!Object.keys(pageSettings).length) {
                    return;
                }

                self.isPgParticlesEnabled();

                if (!this.isAddonEnabled) {
                    return;
                }


                self.generateParticles();

                var pageStyle = pageSettings.premium_particles_custom_style,
                    pageZIndex = pageSettings.premium_particles_zindex,
                    pageDevice = pageSettings.premium_particles_responsive;

                if (!pageStyle) return !1;

                var p = {};
                p.style = JSON.parse(pageStyle);
                p.zindex = pageZIndex;
                p.devices = pageDevice;
                p = Object.assign(p);

                var n = elementorFrontend.getCurrentDeviceMode();

                var supportedDevices = ['desktop', 'mobile', 'tablet'];

                if (-1 === pageDevice.indexOf(n) && -1 !== supportedDevices.indexOf(n)) return;

                var a = $scope.find('.premium-global-particles');

                a.attr("id", "premium-section-particles"), tsParticles.load("premium-section-particles", p.style), a.children("canvas.tsparticles-canvas-el").css({
                    zIndex: p.zindex
                })

                return p;
            }

            /** Checks if the addon's switcher is enabled and not disabled on the current device mode. */
            self.isPgParticlesEnabled = function () {

                var isAddonEnabled = 'yes' === pageSettings.premium_particles_switcher ? true : false;

                if (isAddonEnabled) {

                    self.isAddonEnabled = true;

                } else {
                    self.isAddonEnabled = false;
                }
            };

            self.generateParticles = function () {

                var elemId = self.getPageId(),
                    uniqueClass = 'premium-global-particles-' + elemId,
                    particlesHtml = self.getParticlesHtml(uniqueClass);

                self.addParticles(uniqueClass, particlesHtml);
            };

            self.getPageId = function () {

                return elementorFrontend.config.post.id;
            };

            self.getParticlesHtml = function (uniqueClass) {

                var particlesHtml = '<div class="premium-global-particles ' + uniqueClass + '">';

                particlesHtml += '</div>';

                return particlesHtml;
            };

            self.addParticles = function (uniqueClass, particles) {

                self.addParticlesClasses();

                $scope.find('.' + uniqueClass).remove();

                $scope.prepend(particles);
            }

            self.addParticlesClasses = function () {

                // remove the old type-class( premium-particles-{type} ) before adding a new one.
                $scope.removeClass(function (index, selector) {
                    return (selector.match(new RegExp("(^|\\s)premium-particles-\\S+", 'g')) || []).join(' ');
                });

                $scope.addClass('premium-particles-yes premium-particles');
            };

        }


    })(jQuery);

    function premiumParticlesHandler(t) {

        var a = t,
            i = a.data("id"),
            tempTarget = a.find('#premium-particles-' + i),
            s = {};

        s = elementorFrontend.isEditMode() && tempTarget.length > 0 ? function (t) {
            var sectionData = tempTarget,
                pStyle = sectionData.data('particles-style'),
                zIndex = sectionData.data('particles-zindex'),
                device = sectionData.data('particles-devices');

            if (!pStyle) return !1;

            if (s.zindex = zIndex, s.style = pStyle, s.devices = device.split(" "), 0 !== Object.keys(s).length)
                return s

        }(i) : function () {
            var e = a.data();

            if (!e.particlesStyle) return !1;
            if (s.zindex = e.particlesZindex, s.style = e.particlesStyle, s.devices = e.particlesDevices.split(" "), 0 !== Object.keys(s).length) return s
        }();

        if (!s) return !1;
        var n = elementorFrontend.getCurrentDeviceMode();

        var supportedDevices = ['desktop', 'mobile', 'tablet'];
        if (-1 === s.devices.indexOf(n) && -1 !== supportedDevices.indexOf(n)) return;

        a.attr("id", "premium-section-particles-" + i), tsParticles.load("premium-section-particles-" + i, s.style), a.children("canvas.tsparticles-canvas-el").css({
            zIndex: s.zindex
        })
    }

}(jQuery);