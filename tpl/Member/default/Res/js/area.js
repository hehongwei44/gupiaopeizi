/**
 * 省份城市区域JS类
 * 
 * @param {}
 *            baseUrl 获取json结构体 获取省份城市的Ajax基础链接
 * @param {}
 *            orgProvince 默认省份
 * @param {}
 *            orgcity 默认城市
 * @param {}
 *            provinceId 省份绑定select id
 * @param {}
 *            cityId 城市绑定select id
 */
function Area(baseUrl, orgProvince, orgCity, orgArea, provinceId, cityId,
		areaId) {
	var _this = this;
	this._baseUrl = baseUrl;
	this._orgProvince = orgProvince;
	this._orgCity = orgCity;
	this._orgArea = orgArea;
	this._provinceId = "#" + provinceId;
	this._cityId = "#" + cityId;
	this._areaId = "#" + areaId;
	this.provinceList;
	this.cityList = new Array();
	this.areaList = new Array();

	/**
	 * 获取省份信息
	 */
	this.getProvince = function() {
		if (!_this.provinceList) {
			jQuery.ajax({
						type : "GET",
						url : _this._baseUrl + "/queryProvince.htm",
						dataType : "json",
						async : false,
						success : function(list) {
							_this.provinceList = list;
						}
					});
		}
		return _this.provinceList;
	}

	/**
	 * 获取城市信息
	 */
	this.getCity = function(province) {
		if (province && province.length > 0) {
			if (!_this.cityList || !_this.cityList[province]) {
				jQuery.ajax({
							type : "GET",
							url : _this._baseUrl + "/queryCity.htm",
							dataType : "json",
							data : "province=" + province,
							async : false,
							success : function(list) {
								if (list) {
									_this.cityList[province] = list;
								}
							}
						});
			}
			if (_this.cityList && _this.cityList[province]) {
				return _this.cityList[province];
			}
		}

	}

	/**
	 * 获取区域信息
	 */
	this.getArea = function(city) {
		if (city && city.length > 0) {
			if (!_this.areaList || !_this.areaList[city]) {
				jQuery.ajax({
							type : "GET",
							url : _this._baseUrl + "/queryArea.htm",
							dataType : "json",
							data : "city=" + city,
							async : false,
							success : function(list) {
								if (list) {
									_this.areaList[city] = list;
								}
							}
						});
			}
			if (_this.areaList && _this.areaList[city]) {
				return _this.areaList[city];
			}
		}
	}

	/**
	 * 建立省份列表
	 */
	this.buildProvince = function() {
		var option = "";
		$(_this._provinceId).empty();
		$(_this._provinceId)
				.append('<option value="">&#35831;&#36873;&#25321;</option>');
		var list = _this.getProvince();
		var title = "";
		if (list) {
			for (var i in list) {
				var item = list[i];
				if (_this._orgProvince == item.value) {
					option = '<option selected title="'+item.name +'" value="' + item.value + '">'
							+ item.name + '</option>';
					title = item.name;
				} else {
					option = '<option title="'+item.name +'" value="' + item.value + '">' + item.name
							+ '</option>';
				}
				$(_this._provinceId).append(option);
				$(_this._provinceId).attr("title",title);
			}
		}
	}

	/**
	 * 建立城市列表
	 */
	this.buildCity = function(province) {
		var option = "";
		$(_this._cityId).empty();
		$(_this._cityId)
				.append('<option value="">&#35831;&#36873;&#25321;</option>');
		var list = _this.getCity(province);
		var title = "";
		if (list) {
			for (var i in list) {
				var item = list[i];
				if (_this._orgCity == item.value) {
					option = '<option selected title="'+item.name +'" value="' + item.value + '">'
							+ item.name + '</option>';
					var title = item.name;
				} else {
					option = '<option title="'+item.name +'"  value="' + item.value + '">' + item.name
							+ '</option>';
				}
				$(_this._cityId).append(option);
				$(_this._cityId).attr("title",title);
			}
		}
	}

	/**
	 * 建立区域列表
	 */
	this.buildArea = function(city) {
		var option = "";
		$(_this._areaId).empty();
		$(_this._areaId)
				.append('<option value="">&#35831;&#36873;&#25321;</option>');
		var list = _this.getArea(city);
		var title = "";
		if (list) {
			for (var i in list) {
				var item = list[i];
				if (_this._orgArea == item.value) {
					option = '<option selected title="'+item.name +'" value="' + item.value + '">'
							+ item.name + '</option>';
					var title = item.name;
				} else {
					option = '<option  title="'+item.name +'" value="' + item.value + '">' + item.name
							+ '</option>';
				}
				$(_this._areaId).append(option);
				$(_this._areaId).attr("title",title);
			}
		}
	}

	this.provinceChange = function() {
		_this.buildCity($(_this._provinceId).val());
		_this.buildArea($(_this._cityId).val());
		try{
			var sele = _this._provinceId + " option:selected";
			$(_this._provinceId).attr("title",$(sele).text() );
		}catch(e){}
	}

	this.cityChange = function() {
		_this.buildArea($(_this._cityId).val());
		try{
			var sele = _this._cityId + " option:selected";
			$(_this._cityId).attr("title",$(sele).text() );
		}catch(e){}
	}
	
	this.areaChange = function(){
		try{
			var sele = _this._areaId + " option:selected";
			$(_this._areaId).attr("title",$(sele).text() );
		}catch(e){}
	}

	/**
	 * 运行函数
	 */
	this.run = function() {
		
		_this.buildProvince();
		_this.buildCity($(_this._provinceId).val());
		_this.buildArea($(_this._cityId).val());
	}

	$(function() {
		
				$(_this._provinceId).change(function() {
							_this.provinceChange();
						});
				$(_this._cityId).change(function() {
							_this.cityChange();
						});
				$(_this._areaId).change(function() {
							_this.areaChange();
						});
				_this.run();
			});
}
